<?php

namespace App\Http\Controllers;

use App\Models\AccountingRequest;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get pending payments (DV processed requests)
        $pendingQuery = AccountingRequest::with('budgetRequest')
            ->where('status', 'dv_processed')
            ->whereNull('payment_type')
            ->latest();

        // Log the pending query
        \Log::info('Pending Requests Query:', [
            'query' => $pendingQuery->toSql(),
            'bindings' => $pendingQuery->getBindings()
        ]);

        $pendingRequests = $pendingQuery->paginate(10, ['*'], 'pending');

        // Get processed payments
        $processedQuery = AccountingRequest::with('budgetRequest')
            ->where('status', 'dv_processed')
            ->whereNotNull('payment_type')
            ->latest();

        // Log the processed query
        \Log::info('Processed Requests Query:', [
            'query' => $processedQuery->toSql(),
            'bindings' => $processedQuery->getBindings()
        ]);

        $processedRequests = $processedQuery->paginate(10, ['*'], 'processed');

        // Log the counts for verification
        \Log::info('Request Counts:', [
            'pending' => $pendingRequests->total(),
            'processed' => $processedRequests->total()
        ]);

        return view('cashier.index', compact('pendingRequests', 'processedRequests'));
    }

    public function process($id, Request $request)
    {
        $accountingRequest = AccountingRequest::with('budgetRequest')->findOrFail($id);
        $type = $request->query('type', 'check');

        if (!in_array($type, ['check', 'lddap'])) {
            return redirect()->route('cashier.index')
                ->with('error', 'Invalid payment type specified.');
        }

        return view('cashier.process', [
            'request' => $accountingRequest,
            'type' => $type
        ]);
    }

    public function store($id, Request $request)
    {
        try {
            $accountingRequest = AccountingRequest::findOrFail($id);
            
            // Log initial state
            \Log::info('Processing payment for accounting request:', [
                'id' => $id,
                'current_status' => $accountingRequest->status,
                'current_payment_type' => $accountingRequest->payment_type
            ]);
            
            // Validate the request
            $validated = $request->validate([
                'payment_type' => 'required|in:check,lddap',
                'reference_no' => 'required|string|max:255',
                'payment_date' => 'required|date',
                'tax' => 'nullable|numeric|min:0',
                'payment_remarks' => 'nullable|string',
            ]);

            // Clean the reference number (remove commas)
            $referenceNo = str_replace(',', '', $validated['reference_no']);

            // Force update using DB transaction
            \DB::transaction(function() use ($accountingRequest, $validated, $referenceNo) {
                // Log before update
                \Log::info('Before updating accounting request:', [
                    'id' => $accountingRequest->id,
                    'status' => $accountingRequest->status,
                    'payment_type' => $accountingRequest->payment_type,
                    'tax_amount' => $validated['tax'] ?? 0
                ]);

                // Log payment details before save
                \Log::info('Payment details to be saved:', [
                    'payment_type' => $validated['payment_type'],
                    'reference_no' => $referenceNo,
                    'payment_date' => $validated['payment_date'],
                    'tax_amount' => $validated['tax'] ?? 0
                ]);

                $accountingRequest->forceFill([
                    'payment_type' => $validated['payment_type'],
                    'reference_no' => $referenceNo,
                    'payment_date' => $validated['payment_date'],
                    'tax' => $validated['tax'] ?? 0,
                    'payment_remarks' => $validated['payment_remarks'],
                    'date_processed' => now(),
                    'status' => 'dv_processed'
                ])->save();

                // Log after update
                $accountingRequest->refresh();
                \Log::info('Final state after payment processing:', [
                    'id' => $accountingRequest->id,
                    'status' => $accountingRequest->status,
                    'payment_type' => $accountingRequest->payment_type,
                    'tax_amount' => $accountingRequest->tax
                ]);

                // Update the budget request and purchase request status
                $accountingRequest->budgetRequest->update(['status' => 'paid']);
                $accountingRequest->budgetRequest->purchaseRequest->update(['status' => 'paid']);
            });

            return redirect()->route('cashier.index')
                ->with('success', 'Payment has been processed successfully.');
        } catch (\Exception $e) {
            \Log::error('Payment processing error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $id,
                'request_data' => $request->all()
            ]);
            return redirect()->route('cashier.index')
                ->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }
} 