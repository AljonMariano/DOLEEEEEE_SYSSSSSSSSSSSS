<?php

namespace App\Http\Controllers;

use App\Models\BudgetRequest;
use App\Models\PurchaseOrder;
use App\Models\AccountingRequest;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get pending budget requests that haven't been acknowledged by accounting
        $pendingRequests = BudgetRequest::with(['purchaseRequest'])
            ->where('status', 'acknowledged')
            ->whereNotExists(function ($query) {
                $query->select('id')
                    ->from('accounting_requests')
                    ->whereColumn('budget_request_id', 'budget_requests.id');
            })
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        // Get acknowledged requests
        $acknowledgedRequests = AccountingRequest::with(['budgetRequest.purchaseRequest'])
            ->where('status', 'acknowledged')
            ->latest()
            ->paginate(10, ['*'], 'acknowledged_page');

        // Get requests ready for DV processing
        $processedRequests = AccountingRequest::with(['budgetRequest.purchaseRequest'])
            ->where('status', 'for_payment')
            ->latest()
            ->paginate(10, ['*'], 'processed_page');

        // Get accepted requests (with DV numbers)
        $acceptedRequests = AccountingRequest::with(['budgetRequest.purchaseRequest'])
            ->where('status', 'dv_processed')
            ->latest()
            ->paginate(10, ['*'], 'accepted_page');

        // Get rejected requests
        $rejectedRequests = AccountingRequest::with(['budgetRequest.purchaseRequest'])
            ->where('status', 'rejected')
            ->latest()
            ->paginate(10, ['*'], 'rejected_page');

        return view('accounting.index', compact(
            'pendingRequests', 
            'acknowledgedRequests', 
            'processedRequests',
            'acceptedRequests',
            'rejectedRequests'
        ));
    }

    public function acknowledge($id)
    {
        $budgetRequest = BudgetRequest::with('purchaseRequest')->findOrFail($id);
        return view('accounting.acknowledge', compact('budgetRequest'));
    }

    public function store(Request $request)
    {
        $budgetRequest = BudgetRequest::findOrFail($request->budget_request_id);

        // Validate the request
        $validated = $request->validate([
            'po_no' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (AccountingRequest::where('po_no', $value)->exists()) {
                        $fail('This PO Number has already been used.');
                    }
                }
            ],
            'po_date' => 'required|date',
            'amount' => [
                'required', 
                'numeric', 
                'min:0', 
                'max:' . $budgetRequest->amount
            ],
        ], [
            'po_no.required' => 'The PO Number is required.',
            'po_no.unique' => 'This PO Number has already been used.',
            'amount.max' => 'The amount cannot exceed ₱' . number_format($budgetRequest->amount, 2),
        ]);

        try {
            // Create accounting request
            $accountingRequest = new AccountingRequest([
                'budget_request_id' => $budgetRequest->id,
                'ors_no' => $budgetRequest->ors_no,
                'po_no' => $validated['po_no'],
                'po_date' => $validated['po_date'],
                'payee' => $budgetRequest->payee,
                'amount' => $validated['amount'],
                'date_processed' => now(),
                'status' => 'acknowledged'
            ]);

            $accountingRequest->save();

            // Update budget request status
            $budgetRequest->update(['status' => 'processed']);
            $budgetRequest->purchaseRequest->update(['status' => 'processed']);

            return redirect()->route('accounting.index')
                ->with('success', 'Budget Request has been processed successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'An error occurred while processing the request. Please try again.'
            ]);
        }
    }

    public function show(AccountingRequest $accounting)
    {
        return view('accounting.show', compact('accounting'));
    }

    public function processDv(Request $request, $id)
    {
        $accountingRequest = AccountingRequest::findOrFail($id);
        $action = $request->query('action');

        if ($action === 'update') {
            return view('accounting.process-dv', [
                'request' => $accountingRequest,
                'action' => 'update',
                'showButtons' => true
            ]);
        } elseif ($action === 'accept') {
            return view('accounting.process-dv', [
                'request' => $accountingRequest,
                'action' => 'accept'
            ]);
        } elseif ($action === 'reject') {
            return view('accounting.process-dv', [
                'request' => $accountingRequest,
                'action' => 'reject'
            ]);
        }
    }

    public function storeDv(Request $request, $id)
    {
        $accountingRequest = AccountingRequest::findOrFail($id);
        
        $validated = $request->validate([
            'action' => 'required|in:accept,reject,update',
            'dv_no' => [
                'required_if:action,accept',
                function ($attribute, $value, $fail) use ($accountingRequest) {
                    if ($value) {
                        // Check if DV number exists
                        $existingDV = AccountingRequest::where('dv_no', $value)
                            ->where('id', '!=', $accountingRequest->id)
                            ->first();
                        
                        if ($existingDV) {
                            // If DV exists, check if it's for the same payee and amount
                            if ($existingDV->payee !== $accountingRequest->payee || 
                                $existingDV->amount != $accountingRequest->amount) {
                                $fail('This DV number has already been used for a different payee or amount. Please use a different DV number.');
                            } else {
                                // If same payee and amount, show informational message
                                session()->flash('info', 'This DV number is being used for related transactions with the same payee and amount.');
                            }
                        }
                    }
                }
            ],
            'remarks' => [
                'required_if:action,reject',
                'string',
                'min:10'
            ],
        ], [
            'dv_no.required_if' => 'The DV Number is required when accepting a request.',
            'remarks.required_if' => 'Please provide remarks explaining why the request is being rejected.',
            'remarks.min' => 'The remarks must be at least 10 characters long.',
        ]);

        try {
            if ($validated['action'] === 'accept') {
                $accountingRequest->update([
                    'dv_no' => $validated['dv_no'],
                    'dv_date' => now(),
                    'status' => 'dv_processed'
                ]);
                $message = 'Request has been accepted and DV has been processed successfully.';
            } elseif ($validated['action'] === 'reject') {
                $accountingRequest->update([
                    'remarks' => $validated['remarks'],
                    'status' => 'rejected'
                ]);
                $message = 'Request has been rejected successfully.';
            }

            return redirect()->route('accounting.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'An error occurred while processing the request. Please try again.'
            ]);
        }
    }
} 