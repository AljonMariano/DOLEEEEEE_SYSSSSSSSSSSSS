@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Process Budget Request
                    </h2>
                    <a href="{{ route('accounting.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Back to List
                    </a>
                </div>

                <form action="{{ route('accounting.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="budget_request_id" value="{{ $budgetRequest->id }}">

                    <!-- Original Budget Request Details -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Budget Request Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PR No.</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $budgetRequest->pr_no }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ORS No.</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $budgetRequest->ors_no }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ORS Date</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $budgetRequest->ors_date ? $budgetRequest->ors_date->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payee</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $budgetRequest->payee }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Purpose</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $budgetRequest->purpose }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount</label>
                                <p class="mt-1 text-sm text-gray-900">₱{{ number_format($budgetRequest->amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Accounting Department Fields -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- PO No. -->
                            <div>
                                <label for="po_no" class="block text-sm font-medium text-gray-700">PO No.</label>
                                <input type="text" name="po_no" id="po_no" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('po_no') border-red-500 @enderror"
                                    value="{{ old('po_no') }}">
                                @error('po_no')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- PO Date -->
                            <div>
                                <label for="po_date" class="block text-sm font-medium text-gray-700">PO Date</label>
                                <input type="date" name="po_date" id="po_date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('po_date') border-red-500 @enderror"
                                    value="{{ old('po_date') }}">
                                @error('po_date')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" required step="0.01" 
                                        max="{{ $budgetRequest->amount }}"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('amount') border-red-500 @enderror"
                                        value="{{ old('amount', $budgetRequest->amount) }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Maximum amount: ₱{{ number_format($budgetRequest->amount, 2) }}</p>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('accounting.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Process Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 