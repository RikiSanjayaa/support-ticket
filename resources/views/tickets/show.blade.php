<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ticket Details') }}
            </h2>
            <x-primary-button>
                <a href="{{ route('tickets.edit', $ticket) }}">
                    {{ __('Edit Ticket') }}
                </a>
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium">{{ __('Title') }}</h3>
                            <p class="mt-1">{{ $ticket->title }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium">{{ __('Description') }}</h3>
                            <p class="mt-1">{{ $ticket->description }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium">{{ __('Status') }}</h3>
                            <p class="mt-1">{{ ucfirst($ticket->status) }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium">{{ __('Created At') }}</h3>
                            <p class="mt-1">{{ $ticket->created_at->format('F j, Y, g:i a') }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium">{{ __('Assigned To') }}</h3>
                            <p class="mt-1">{{ $ticket->assignedAgent?->name ?? 'Unassigned' }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium">{{ __('Created By') }}</h3>
                            <p class="mt-1">{{ $ticket->creator?->name ?? 'Unknown' }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium">{{ __('Created At') }}</h3>
                            <p class="mt-1">{{ $ticket->created_at->format('F j, Y, g:i a') }}</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="{{ route('tickets.index') }}"
                                class="text-gray-600 dark:text-gray-400">{{ __('Back to List') }}</a>
                        </div>

                        @if (auth()->user()->role === 'agent' && $ticket->status !== 'closed')
                            <div class="flex items-center gap-4">
                                <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="inline">
                                    @csrf
                                    <x-primary-button type="submit">
                                        {{ $ticket->assigned_to === auth()->id() ? __('Unassign Me') : __('Assign to Me') }}
                                    </x-primary-button>
                                </form>

                                @if ($ticket->assigned_to === auth()->id())
                                    <form action="{{ route('tickets.resolve', $ticket) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <x-secondary-button type="submit">
                                            {{ __('Mark as Resolved') }}
                                        </x-secondary-button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        @if ($ticket->status === 'closed')
                            <div>
                                <h3 class="text-lg font-medium">{{ __('Resolved By') }}</h3>
                                <p class="mt-1">{{ $ticket->resolvedBy?->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $ticket->resolved_at?->format('F j, Y, g:i a') ?? '' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
