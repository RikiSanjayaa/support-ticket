<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Regular User Dashboard --}}
            @if (auth()->user()->role === 'user')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('My Tickets') }}</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Submit a new support request.') }}
                            </p>

                            <form method="POST" action="{{ route('tickets.store') }}" class="mt-6 space-y-6">
                                @csrf

                                <div>
                                    <x-input-label for="title" :value="__('Title')" />
                                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                        required />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="description" :value="__('Description')" />
                                    <x-textarea-input id="description" name="description" class="mt-1 block w-full"
                                        rows="4" required />
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Submit Ticket') }}</x-primary-button>

                                    @if (session('status') === 'ticket-created')
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Ticket created successfully.') }}
                                        </p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Recent Tickets') }}
                            </h3>
                            @if ($tickets?->count() > 0)
                                <div class="mt-4 space-y-4">
                                    @foreach ($tickets as $ticket)
                                        <div class="border-b dark:border-gray-700 pb-4">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $ticket->title }}</h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $ticket->created_at->diffForHumans() }}</p>
                                                </div>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ match ($ticket->status) {
                                                        'open' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                        'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                        'closed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                    } }}">
                                                    {{ ucfirst($ticket->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-4 text-gray-600 dark:text-gray-400">{{ __('No tickets found.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Agent Dashboard --}}
            @elseif(auth()->user()->role === 'agent')
                <div class="space-y-6">
                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Assigned Tickets') }}</h3>
                                <p class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                                    {{ $assignedTickets ?? 0 }}
                                </p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Open Tickets') }}</h3>
                                <p class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                                    {{ $openTickets ?? 0 }}
                                </p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('In Progress') }}</h3>
                                <p class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                                    {{ $inProgressTickets ?? 0 }}
                                </p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Resolved Today') }}</h3>
                                <p class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                                    {{ $resolvedToday ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Latest Support History --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Latest Support History') }}</h3>
                                <x-secondary-button onclick="window.location.href='{{ route('tickets.index') }}'">
                                    {{ __('View All') }}
                                </x-secondary-button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Ticket ID') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Title') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Status') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Created') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Actions') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($recentTickets ?? [] as $ticket)
                                            <tr>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    #{{ $ticket->id }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $ticket->title }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ match ($ticket->status) {
                                                'open' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'closed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            } }}">
                                                        {{ ucfirst($ticket->status) }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $ticket->created_at->format('d/m/Y') }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    <div class="flex space-x-2">
                                                        <x-primary-button class="px-3 py-1"
                                                            onclick="window.location.href='{{ route('tickets.show', $ticket) }}'">
                                                            {{ __('View') }}
                                                        </x-primary-button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5"
                                                    class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    {{ __('No tickets found') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Admin Dashboard --}}
            @else
                {{-- Admin Panel Link --}}
                <div class="mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Admin Tools') }}</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Access the full admin panel for advanced management.') }}
                                    </p>
                                </div>
                                <x-primary-button onclick="window.location.href='/admin'" class="ml-4">
                                    {{ __('Open Admin Panel') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Stats Overview --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Total Tickets') }}
                            </h3>
                            <p class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                                {{ $totalTickets ?? 0 }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Open Tickets') }}
                            </h3>
                            <p class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                                {{ $openTickets ?? 0 }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Agents Available') }}</h3>
                            <p class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                                {{ $agentsCount ?? 0 }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Response Rate') }}
                            </h3>
                            <p class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                                {{ $responseRate ?? '0%' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Agent Performance --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Agent Performance') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Agent') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Assigned Tickets') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Resolved Tickets') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Response Time (Avg)') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Resolution Rate') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($agentPerformance ?? [] as $agent)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $agent->name }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $agent->assigned_count }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $agent->resolved_count }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $agent->average_response_time ?? 'N/A' }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                <div class="flex items-center">
                                                    <div class="w-16">{{ $agent->resolution_rate }}%</div>
                                                    <div class="flex-1 h-2 ml-2 bg-gray-200 rounded-full">
                                                        <div class="h-2 bg-green-500 rounded-full"
                                                            style="width: {{ $agent->resolution_rate }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5"
                                                class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('No agent data available') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
