<?php

namespace App\Http\Controllers;

use App\Models\BudgetRequest;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get pending purchase requests that haven't been acknowledged
        $pendingRequests = PurchaseRequest::where('status', 'pending')
            ->whereNotExists(function ($query) {
                $query->select('id')
                    ->from('budget_requests')
                    ->whereColumn('purchase_request_id', 'purchase_requests.id');
            })
            ->latest()
            ->paginate(10);

        // Get acknowledged requests (budget requests)
        $acknowledgedRequests = BudgetRequest::with('purchaseRequest')
            ->latest()
            ->paginate(10);

        return view('budget.index', compact('pendingRequests', 'acknowledgedRequests'));
    }

    public function acknowledge($id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);
        
        return view('budget.acknowledge', compact('purchaseRequest'));
    }

    public function store(Request $request)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($request->purchase_request_id);

        // Validate the request
        $validated = $request->validate([
            'ors_no' => 'required|string|max:255',
            'ors_date' => 'required|date',
            'payee' => 'required|string|max:255',
            'amount' => ['required', 'numeric', 'min:0', 'max:' . $purchaseRequest->amount],
        ]);

        // Create budget request
        $budgetRequest = new BudgetRequest([
            'purchase_request_id' => $purchaseRequest->id,
            'pr_no' => $purchaseRequest->pr_no,
            'pr_date' => $purchaseRequest->pr_date,
            'purpose' => $purchaseRequest->purpose,
            'ors_no' => $validated['ors_no'],
            'ors_date' => $validated['ors_date'],
            'payee' => $validated['payee'],
            'amount' => $validated['amount'],
            'date_processed' => now(),
            'status' => 'acknowledged'
        ]);

        $budgetRequest->save();

        // Update purchase request status
        $purchaseRequest->update(['status' => 'acknowledged']);

        return redirect()->route('budget.index')
            ->with('success', 'Purchase Request has been acknowledged and processed.');
    }

    public function show(BudgetRequest $budget)
    {
        return view('budget.show', compact('budget'));
    }
} 