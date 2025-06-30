<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Support Tickets') }}
            </h2>
            <x-primary-button>
                <a href="{{ route('tickets.create') }}">
                    {{ __('Create New Ticket') }}
                </a>
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Title</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Status</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Assigned To</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Created By</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Created</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $ticket->title }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ match ($ticket->status) {
                                                'open' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'closed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                default => 'bg-gray-100 text-gray-800',
                                            } }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $ticket->assignedAgent?->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $ticket->creator?->name ?? 'Unknown' }}
                                    <td class="px-6 py-4">{{ $ticket->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            {{-- View button - visible to all users --}}
                                            <x-primary-button class="w-20 justify-center">
                                                <a href="{{ route('tickets.show', $ticket) }}">{{ __('View') }}</a>
                                            </x-primary-button>

                                            {{-- Edit button - visible to agents and admins --}}
                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'agent')
                                                <x-secondary-button class="w-20 justify-center">
                                                    <a
                                                        href="{{ route('tickets.edit', $ticket) }}">{{ __('Edit') }}</a>
                                                </x-secondary-button>
                                            @endif

                                            {{-- Delete button - visible only to admins --}}
                                            @if (auth()->user()->role === 'admin')
                                                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button type="submit" class="w-20 justify-center"
                                                        onclick="return confirm('Are you sure you want to delete this ticket?')">
                                                        {{ __('Delete') }}
                                                    </x-danger-button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
