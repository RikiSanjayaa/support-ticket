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
            {{-- Add Search and Filter Section --}}
            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" class="space-y-4" id="search-form">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="search" :value="__('Search')" />
                                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full"
                                    :value="request('search')" placeholder="Search by title..." autocomplete="off" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="open" @selected(request('status') == 'open')>Open</option>
                                    <option value="in_progress" @selected(request('status') == 'in_progress')>In Progress</option>
                                    <option value="closed" @selected(request('status') == 'closed')>Closed</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="created_by" :value="__('Created By')" />
                                <select id="created_by" name="created_by"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Users</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(request('created_by') == $user->id)>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="assigned_to" :value="__('Assigned To')" />
                                <select id="assigned_to" name="assigned_to"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Agents</option>
                                    <option value="unassigned" @selected(request('assigned_to') == 'unassigned')>Unassigned</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}" @selected(request('assigned_to') == $agent->id)>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Hidden inputs for sorting --}}
                        <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                        <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
                    </form>
                </div>
            </div>

            {{-- Display Tickets --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Title</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Status</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Assigned To</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">Created By</th>
                                <th class="px-6 py-3 text-left text-gray-900 dark:text-gray-100">
                                    <div class="flex items-center space-x-1">
                                        <span>Created</span>
                                        <button type="button" onclick="toggleSort('created_at')"
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            @if (request('sort') == 'created_at')
                                                @if (request('direction') == 'asc')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3" />
                                                    </svg>
                                                @endif
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                </svg>
                                            @endif
                                        </button>
                                    </div>
                                </th>
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

<script>
    // Debounce function to limit how often the search runs
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Handle form submission
    function submitForm() {
        document.getElementById('search-form').submit();
    }

    // Handle sorting
    function toggleSort(column) {
        const form = document.getElementById('search-form');
        const sortInput = form.querySelector('input[name="sort"]');
        const directionInput = form.querySelector('input[name="direction"]');

        if (sortInput.value === column) {
            // Toggle direction if same column
            directionInput.value = directionInput.value === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new column with default desc direction
            sortInput.value = column;
            directionInput.value = 'desc';
        }

        submitForm();
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const statusSelect = document.getElementById('status');
        const createdBySelect = document.getElementById('created_by');
        const assignedToSelect = document.getElementById('assigned_to');

        // Debounced search for text input (500ms delay)
        const debouncedSubmit = debounce(() => submitForm(), 500);
        searchInput.addEventListener('input', debouncedSubmit);

        // Immediate search for dropdowns
        statusSelect.addEventListener('change', submitForm);
        createdBySelect.addEventListener('change', submitForm);
        assignedToSelect.addEventListener('change', submitForm);
    });
</script>
