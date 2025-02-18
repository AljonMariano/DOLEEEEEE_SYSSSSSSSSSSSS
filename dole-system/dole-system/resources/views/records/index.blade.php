@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Record Type Navigation -->
            <div class="mb-6">
                <nav class="flex space-x-4 border-b border-gray-200">
                    <a href="{{ route('records.index') }}" 
                        class="px-3 py-2 text-sm font-medium text-blue-700 border-b-2 border-blue-500">
                        Purchase Requests
                    </a>
                </nav>
            </div>

            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Purchase Requests') }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Total Records: {{ \App\Models\PurchaseRequest::count() }}
                    </p>
                </div>
                <a href="{{ route('records.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add New Purchase Request
                </a>
            </div>

            <!-- Purchase Requests Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto w-full">
                        <table class="w-full table-fixed divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR No.</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR Date</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="w-3/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Processed</th>
                                    <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($purchaseRequests as $pr)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $pr->pr_no }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pr->pr_date ? $pr->pr_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ₱{{ number_format($pr->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ Str::limit($pr->purpose, 50) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $pr->status === 'acknowledged' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($pr->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pr->date_processed ? $pr->date_processed->format('M d, Y g:i A') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('records.show', $pr) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('records.edit', $pr) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('records.destroy', $pr) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this purchase request?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                                            No purchase requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $purchaseRequests->links() }}
                    </div>
                </div>
            </div>

            <!-- Processed Requests Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Processed Requests</h3>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full table-fixed divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR No.</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR Date</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ORS No.</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ORS Date</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO No.</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Date</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payee</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($processedRequests ?? [] as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->purchaseRequest->pr_no }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->purchaseRequest->pr_date ? $request->purchaseRequest->pr_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->ors_no }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->ors_date ? $request->ors_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->accountingRequest->po_no ?? 'N/A' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->accountingRequest->po_date ? $request->accountingRequest->po_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->payee }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($request->amount, 2) }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-900">{{ Str::limit($request->purpose, 50) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $request->accountingRequest->status === 'dv_processed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($request->accountingRequest->status ?? 'Processed') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($request->accountingRequest && $request->accountingRequest->status === 'acknowledged')
                                                <a href="{{ route('records.for-payment', $request->accountingRequest->id) }}" 
                                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    For Payment
                                                </a>
                                            @elseif($request->accountingRequest && $request->accountingRequest->status === 'for_payment')
                                                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-md text-xs font-semibold uppercase">
                                                    Marked for Payment
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-4 py-4 text-center text-gray-500">
                                            No processed requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($processedRequests))
                        <div class="mt-4">
                            {{ $processedRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 