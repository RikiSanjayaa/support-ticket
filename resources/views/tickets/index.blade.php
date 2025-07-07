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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                                <tr onclick="window.location='{{ route('tickets.show', $ticket) }}'"
                                    class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150">
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
                                    <td class="px-6 py-4">{{ $ticket->assignedAgent?->name ?? 'Unassigned' }}</td>
                                    <td class="px-6 py-4">{{ $ticket->creator?->name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4">{{ $ticket->created_at->format('Y-m-d') }}</td>
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
