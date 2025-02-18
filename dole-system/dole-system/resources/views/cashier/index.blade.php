@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
            {{ __('Cashier Department') }}
        </h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Pending Payments -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Payments</h3>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ORS No.</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DV No.</th>
                                <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payee</th>
                                <th class="w-3/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pendingRequests ?? [] as $request)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->ors_no }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->dv_no }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->payee }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->budgetRequest->purpose }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">₱{{ number_format($request->amount, 2) }}</td>
                                    <td class="px-4 py-4 text-sm font-medium space-x-2">
                                        <a href="{{ route('cashier.process', ['id' => $request->id, 'type' => 'check']) }}"
                                            style="display: inline-block; padding: 8px 16px; background-color: #2563eb; color: white; font-weight: bold; border-radius: 6px; text-decoration: none;">
                                            Check
                                        </a>
                                        <a href="{{ route('cashier.process', ['id' => $request->id, 'type' => 'lddap']) }}"
                                            style="display: inline-block; padding: 8px 16px; background-color: #22c55e; color: white; font-weight: bold; border-radius: 6px; text-decoration: none;">
                                            LDDAP
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                        No pending payments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if(isset($pendingRequests) && $pendingRequests->hasPages())
                        <div class="mt-4">
                            {{ $pendingRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Processed Payments -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Processed Payments</h3>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ORS No.</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DV No.</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Type</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference No.</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payee</th>
                                <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($processedRequests ?? [] as $request)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->ors_no }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->dv_no }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        @if($request->payment_type === 'check')
                                            <div style="background-color: #2563eb; color: white; padding: 6px 12px; border-radius: 4px; display: inline-flex; align-items: center; font-weight: 600; width: fit-content;">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                                </svg>
                                                CHECK
                                            </div>
                                        @else
                                            <div style="background-color: #22c55e; color: white; padding: 6px 12px; border-radius: 4px; display: inline-flex; align-items: center; font-weight: 600; width: fit-content;">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                                </svg>
                                                LDDAP
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->reference_no }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        {{ $request->payment_date ? $request->payment_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->payee }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $request->budgetRequest->purpose }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">₱{{ number_format($request->amount, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        ₱{{ number_format($request->tax ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                        ₱{{ number_format(($request->amount - ($request->tax ?? 0)), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-4 text-center text-gray-500">
                                        No processed payments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if(isset($processedRequests) && $processedRequests->hasPages())
                        <div class="mt-4">
                            {{ $processedRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 