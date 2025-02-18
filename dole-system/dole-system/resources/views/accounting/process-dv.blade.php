@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('info'))
            <div class="mb-4 px-4 py-3 bg-blue-100 border border-blue-400 text-blue-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
            {{ __('Process DV Request') }}
        </h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Request Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">PR No:</p>
                            <p class="font-medium">{{ $request->budgetRequest->purchaseRequest->pr_no }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Amount:</p>
                            <p class="font-medium">₱{{ number_format($request->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payee:</p>
                            <p class="font-medium">{{ $request->payee }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Purpose:</p>
                            <p class="font-medium">{{ $request->budgetRequest->purpose }}</p>
                        </div>
                        @if($request->remarks)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Previous Remarks:</p>
                            <p class="font-medium text-red-600">{{ $request->remarks }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if(isset($showButtons) && $showButtons)
                <div class="flex justify-center space-x-4">
                    <button onclick="window.location.href='{{ route('accounting.process-dv', ['id' => $request->id, 'action' => 'accept']) }}'" 
                        style="background-color: #2563eb; color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold;">
                        Accept
                    </button>
                    <button onclick="window.location.href='{{ route('accounting.process-dv', ['id' => $request->id, 'action' => 'reject']) }}'" 
                        style="background-color: #dc2626; color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold;">
                        Reject
                    </button>
                </div>
                @else
                <form action="{{ route('accounting.store-dv', $request->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="action" value="{{ $action }}">

                    @if($action === 'accept')
                        <div>
                            <label for="dv_no" class="block text-sm font-medium text-gray-700">DV Number</label>
                            <input type="text" name="dv_no" id="dv_no" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    @elseif($action === 'reject')
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('accounting.index') }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ ucfirst($action) }}
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
        <div class="mt-4">
            @error('dv_no')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mt-4">
            @error('remarks')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
@endsection 