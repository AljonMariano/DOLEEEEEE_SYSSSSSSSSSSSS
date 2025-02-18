@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
            {{ __('Budget Department') }}
        </h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Pending Purchase Requests -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Purchase Requests</h3>
                
                @if($pendingRequests->isEmpty())
                    <p class="text-gray-500">No pending purchase requests found.</p>
                @else
                    <div class="overflow-x-auto w-full">
                        <table class="w-full table-fixed divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR No.</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR Date</th>
                                    <th class="w-4/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingRequests as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->pr_no }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->pr_date ? $request->pr_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900">{{ Str::limit($request->purpose, 100) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($request->amount, 2) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('budget.acknowledge', $request->id) }}" class="text-indigo-600 hover:text-indigo-900">Acknowledge</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pendingRequests->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Acknowledged Requests -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acknowledged Requests</h3>
                
                @if($acknowledgedRequests->isEmpty())
                    <p class="text-gray-500">No acknowledged requests found.</p>
                @else
                    <div class="overflow-x-auto w-full">
                        <table class="w-full table-fixed divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR No.</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR Date</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ORS No.</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ORS Date</th>
                                    <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payee</th>
                                    <th class="w-3/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="w-1/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($acknowledgedRequests as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->pr_no }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->pr_date ? $request->pr_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->ors_no }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->ors_date ? $request->ors_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->payee }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-900">{{ Str::limit($request->purpose, 100) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($request->amount, 2) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('budget.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $acknowledgedRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 