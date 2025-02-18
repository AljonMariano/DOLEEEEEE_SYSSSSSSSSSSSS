<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Statistics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @foreach($sections as $section)
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="rounded-md bg-{{ $section['color'] }}-100 p-3">
                                        @if($section['icon'] === 'document-text')
                                            <svg class="h-6 w-6 text-{{ $section['color'] }}-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        @elseif($section['icon'] === 'clock')
                                            <svg class="h-6 w-6 text-{{ $section['color'] }}-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($section['icon'] === 'check-circle')
                                            <svg class="h-6 w-6 text-{{ $section['color'] }}-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($section['icon'] === 'calendar')
                                            <svg class="h-6 w-6 text-{{ $section['color'] }}-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            {{ $section['name'] }}
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ $section['count'] }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Recent Activity -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Recent Documents
                        </h3>
                        <div class="mt-5">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @forelse($recentDocuments as $document)
                                        <li class="py-4">
                                            <div class="flex space-x-3">
                                                <div class="flex-1 space-y-1">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="text-sm font-medium">
                                                            <a href="{{ route('records.show', $document) }}" class="hover:text-blue-600">
                                                                Document {{ $document->doc_id }}
                                                            </a>
                                                        </h3>
                                                        <p class="text-sm text-gray-500">{{ $document->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    <p class="text-sm text-gray-500">
                                                        {{ Str::limit($document->subject, 100) }}
                                                    </p>
                                                    <p class="text-xs text-gray-400">
                                                        Status: <span class="font-medium {{ $document->status === 'completed' ? 'text-green-600' : 'text-yellow-600' }}">
                                                            {{ ucfirst($document->status) }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="py-4">
                                            <div class="text-center text-gray-500">
                                                No documents yet. <a href="{{ route('records.create') }}" class="text-blue-600 hover:text-blue-800">Create one now</a>
                                            </div>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
