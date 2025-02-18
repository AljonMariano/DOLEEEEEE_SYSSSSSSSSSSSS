<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\BudgetRequest;
use App\Models\AccountingRequest;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        // Get regular purchase requests
        $purchaseRequests = PurchaseRequest::latest()->paginate(10, ['*'], 'purchase_requests');

        // Get processed requests (those that have gone through budget and accounting)
        $processedRequests = BudgetRequest::with(['purchaseRequest', 'accountingRequest'])
            ->whereHas('accountingRequest', function($query) {
                $query->whereIn('status', ['acknowledged', 'processed', 'for_payment', 'dv_processed']);
            })
            ->latest()
            ->paginate(10, ['*'], 'processed_requests');

        // For debugging
        \Log::info('Processed Requests:', [
            'count' => $processedRequests->count(),
            'data' => $processedRequests->toArray()
        ]);

        return view('records.index', compact('purchaseRequests', 'processedRequests'));
    }

    public function create()
    {
        return view('records.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pr_no' => 'required|string|max:255|unique:purchase_requests',
            'pr_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
        ]);

        $validated['status'] = 'pending';
        $validated['date_processed'] = now()->setTimezone('Asia/Manila');

        PurchaseRequest::create($validated);

        return redirect()->route('records.index')
            ->with('success', 'Purchase Request created successfully.');
    }

    public function show(PurchaseRequest $record)
    {
        return view('records.show', compact('record'));
    }

    public function edit(PurchaseRequest $record)
    {
        return view('records.edit', compact('record'));
    }

    public function update(Request $request, PurchaseRequest $record)
    {
        $validated = $request->validate([
            'pr_no' => 'required|string|max:255|unique:purchase_requests,pr_no,' . $record->id,
            'pr_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
        ]);

        $record->update($validated);

        return redirect()->route('records.index')
            ->with('success', 'Purchase Request updated successfully.');
    }

    public function destroy(PurchaseRequest $record)
    {
        $record->delete();

        return redirect()->route('records.index')
            ->with('success', 'Purchase Request deleted successfully.');
    }

    public function markForPayment($id)
    {
        $accountingRequest = AccountingRequest::findOrFail($id);
        $accountingRequest->update([
            'status' => 'for_payment'
        ]);

        return redirect()->route('records.index')
            ->with('success', 'Request has been marked for payment.');
    }
} 