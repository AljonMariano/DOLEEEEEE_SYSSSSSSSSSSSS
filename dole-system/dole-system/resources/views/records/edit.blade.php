@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Record Type Navigation -->
        <div class="mb-6">
            <nav class="flex space-x-4 border-b border-gray-200">
                <a href="{{ route('records.index') }}" 
                    class="px-3 py-2 text-sm font-medium text-blue-700 border-b-2 border-blue-500">
                    Purchase Requests
                </a>
            </nav>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900 mb-6">
                    Edit Purchase Request
                </h2>

                <form action="{{ route('records.update', $record) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- PR No. -->
                        <div>
                            <label for="pr_no" class="block text-sm font-medium text-gray-700">Purchase Request No.</label>
                            <input type="text" name="pr_no" id="pr_no" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('pr_no') border-red-500 @enderror"
                                value="{{ old('pr_no', $record->pr_no) }}">
                            @error('pr_no')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PR Date -->
                        <div>
                            <label for="pr_date" class="block text-sm font-medium text-gray-700">PR Date</label>
                            <input type="date" name="pr_date" id="pr_date" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('pr_date') border-red-500 @enderror"
                                value="{{ old('pr_date', $record->pr_date ? $record->pr_date->format('Y-m-d') : '') }}">
                            @error('pr_date')
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
                                <input type="number" name="amount" id="amount" required step="0.01" min="0"
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('amount') border-red-500 @enderror"
                                    value="{{ old('amount', $record->amount) }}">
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                        <textarea name="purpose" id="purpose" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('purpose') border-red-500 @enderror">{{ old('purpose', $record->purpose) }}</textarea>
                        @error('purpose')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('records.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update Purchase Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 