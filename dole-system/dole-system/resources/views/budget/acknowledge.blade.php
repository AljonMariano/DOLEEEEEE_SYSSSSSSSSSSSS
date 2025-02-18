@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Acknowledge Purchase Request
                    </h2>
                    <a href="{{ route('budget.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Back to List
                    </a>
                </div>

                <form action="{{ route('budget.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="purchase_request_id" value="{{ $purchaseRequest->id }}">

                    <!-- Original Purchase Request Details -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Request Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PR No.</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->pr_no }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PR Date</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $purchaseRequest->pr_date ? $purchaseRequest->pr_date->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Purpose</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->purpose }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Requested Amount</label>
                                <p class="mt-1 text-sm text-gray-900">₱{{ number_format($purchaseRequest->amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Budget Department Fields -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ORS No. -->
                            <div>
                                <label for="ors_no" class="block text-sm font-medium text-gray-700">ORS No.</label>
                                <input type="text" name="ors_no" id="ors_no" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('ors_no') border-red-500 @enderror"
                                    value="{{ old('ors_no') }}">
                                @error('ors_no')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ORS Date -->
                            <div>
                                <label for="ors_date" class="block text-sm font-medium text-gray-700">ORS Date</label>
                                <input type="date" name="ors_date" id="ors_date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('ors_date') border-red-500 @enderror"
                                    value="{{ old('ors_date') }}">
                                @error('ors_date')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payee -->
                            <div>
                                <label for="payee" class="block text-sm font-medium text-gray-700">Payee</label>
                                <input type="text" name="payee" id="payee" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('payee') border-red-500 @enderror"
                                    value="{{ old('payee') }}">
                                @error('payee')
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
                                        max="{{ $purchaseRequest->amount }}"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('amount') border-red-500 @enderror"
                                        value="{{ old('amount', $purchaseRequest->amount) }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Maximum amount: ₱{{ number_format($purchaseRequest->amount, 2) }}</p>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('budget.index') }}" 
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