@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Create New Route Slip') }}
                    </h2>
                    <a href="{{ route('records.route-slips.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
                    </a>
                </div>

                <form action="{{ route('records.route-slips.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Route Slip ID and File Case No -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="slip_id" class="block text-sm font-medium text-gray-700">Route Slip ID</label>
                            <input type="text" name="slip_id" id="slip_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $slipId }}" readonly>
                            @error('slip_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="file_case_no" class="block text-sm font-medium text-gray-700">File/Case No.</label>
                            <input type="text" name="file_case_no" id="file_case_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('file_case_no') }}">
                            @error('file_case_no')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Origin/Source -->
                    <div>
                        <label for="origin" class="block text-sm font-medium text-gray-700">Origin/Source</label>
                        <input type="text" name="origin" id="origin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('origin') }}" required>
                        @error('origin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" id="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('subject') }}" required>
                        @error('subject')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Document Type and Reference -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                            <select name="document_type" id="document_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select Document Type</option>
                                <option value="PO" {{ old('document_type') == 'PO' ? 'selected' : '' }}>Purchase Order</option>
                                <option value="PR" {{ old('document_type') == 'PR' ? 'selected' : '' }}>Purchase Request</option>
                                <option value="Voucher" {{ old('document_type') == 'Voucher' ? 'selected' : '' }}>Voucher</option>
                                <option value="Order" {{ old('document_type') == 'Order' ? 'selected' : '' }}>Order</option>
                            </select>
                            @error('document_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="reference_id" class="block text-sm font-medium text-gray-700">Reference ID</label>
                            <select name="reference_id" id="reference_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select Reference Document</option>
                                @foreach($references as $ref)
                                    <option value="{{ $ref->id }}" {{ old('reference_id') == $ref->id ? 'selected' : '' }}>
                                        {{ $ref->reference_id }} - {{ Str::limit($ref->subject, 50) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reference_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Forward To Department -->
                    <div>
                        <label for="forward_to" class="block text-sm font-medium text-gray-700">Forward To</label>
                        <select name="forward_to" id="forward_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->code }}" {{ old('forward_to') == $dept->code ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('forward_to')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="reset" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Reset
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create Route Slip
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 