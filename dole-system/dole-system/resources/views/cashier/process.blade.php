@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Process {{ ucfirst($type) }} Payment
                    </h2>
                    <a href="{{ route('cashier.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Back to List
                    </a>
                </div>

                <!-- Request Details -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Request Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">ORS No:</p>
                            <p class="font-medium">{{ $request->ors_no }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">DV No:</p>
                            <p class="font-medium">{{ $request->dv_no }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payee:</p>
                            <p class="font-medium">{{ $request->payee }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Amount:</p>
                            <p class="font-medium">₱{{ number_format($request->amount, 2) }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Purpose:</p>
                            <p class="font-medium">{{ $request->budgetRequest->purpose }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('cashier.store', $request->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="payment_type" value="{{ $type }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Reference Number (Check No. or LDDAP No.) -->
                        <div>
                            <label for="reference_no" class="block text-sm font-medium text-gray-700">
                                {{ $type === 'check' ? 'Check Serial Number' : 'LDDAP Serial Number' }}
                            </label>
                            <input type="text" name="reference_no" id="reference_no" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                value="{{ old('reference_no') }}">
                            @error('reference_no')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Date -->
                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-gray-700">
                                {{ $type === 'check' ? 'Check Date' : 'LDDAP Date' }}
                            </label>
                            <input type="date" name="payment_date" id="payment_date" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                value="{{ old('payment_date') }}">
                            @error('payment_date')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gross Amount (Display only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gross Amount</label>
                            <p class="mt-2 text-lg font-bold text-gray-900">₱{{ number_format($request->amount, 2) }}</p>
                        </div>

                        <!-- Tax Amount -->
                        <div>
                            <label for="tax" class="block text-sm font-medium text-gray-700">Tax Amount</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="tax" id="tax" step="0.01" min="0" 
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    value="{{ old('tax') }}"
                                    placeholder="Enter tax amount">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Enter tax amount in pesos</p>
                            @error('tax')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Net Amount (Calculated) -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Net Amount</label>
                            <p class="mt-2 text-xl font-bold text-green-600" id="netAmount">₱{{ number_format($request->amount, 2) }}</p>
                            <p class="mt-1 text-sm text-gray-500">Net amount after tax deduction</p>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label for="payment_remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                        <textarea name="payment_remarks" id="payment_remarks" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('payment_remarks') }}</textarea>
                        @error('payment_remarks')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('cashier.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Calculate net amount when tax changes
    const taxInput = document.getElementById('tax');
    const netAmountDisplay = document.getElementById('netAmount');
    const grossAmount = {{ $request->amount }};

    taxInput.addEventListener('input', function() {
        const taxAmount = parseFloat(this.value) || 0;
        const netAmount = grossAmount - taxAmount;
        netAmountDisplay.textContent = '₱' + netAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    });
</script>
@endpush
@endsection 