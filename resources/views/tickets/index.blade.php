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
                                    <td class="px-6 py-4">{{ $ticket->status }}</td>
                                    <td class="px-6 py-4">
                                        {{ $ticket->assignedAgent?->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $ticket->creator?->name ?? 'Unknown' }}
                                    <td class="px-6 py-4">{{ $ticket->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 space-x-2">
                                        {{-- View button - visible to all users --}}
                                        <x-primary-button>
                                            <a href="{{ route('tickets.show', $ticket) }}">View</a>
                                        </x-primary-button>

                                        {{-- Edit button - visible to agents and admins --}}
                                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'agent')
                                            <x-secondary-button>
                                                <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>
                                            </x-secondary-button>
                                        @endif

                                        {{-- Delete button - visible only to admins --}}
                                        @if (auth()->user()->role === 'admin')
                                            <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this ticket?')">
                                                    {{ __('Delete') }}
                                                </x-danger-button>
                                            </form>
                                        @endif
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
