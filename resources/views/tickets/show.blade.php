<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <x-secondary-button>
                <a href="{{ route('tickets.index') }}" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Tickets') }}
                </a>
            </x-secondary-button>
            <div class="flex gap-2">
                @if (auth()->user()->role === 'agent')
                    @if ($ticket->assigned_to === auth()->id())
                        <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="inline">
                            @csrf
                            <x-secondary-button type="submit">
                                {{ __('Unassign Me') }}
                            </x-secondary-button>
                        </form>
                    @elseif ($ticket->assigned_to === null)
                        <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="inline">
                            @csrf
                            <x-secondary-button type="submit">
                                {{ __('Assign to Me') }}
                            </x-secondary-button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        #{{ $ticket->id }} - {{ $ticket->title }}
                    </h1>
                    <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ match ($ticket->status) {
                                'open' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                'closed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            } }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Opened {{ $ticket->created_at->diffForHumans() }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            By {{ $ticket->creator?->name }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Assigned to: {{ $ticket->assignedAgent?->name ?? 'Unassigned' }}
                        </span>
                        @if ($ticket->status === 'closed')
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Resolved by: {{ $ticket->resolvedBy?->name }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Resolved at: {{ $ticket->resolved_at?->diffForHumans() }}
                            </span>
                        @endif
                    </div>

                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        <div id="ticket-content">
                            {{-- Description Section --}}
                            <div class="flex justify-between items-start w-full mb-6">
                                <div class="flex-grow">
                                    {{ $ticket->description }}
                                    @if ($ticket->updated_at->gt($ticket->created_at))
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mt-2 block">
                                            (edited {{ $ticket->updated_at->diffForHumans() }})
                                        </span>
                                    @endif
                                </div>

                                {{-- Edit Button --}}
                                @if (auth()->user()->role === 'agent' || auth()->user()->role === 'admin' || auth()->id() === $ticket->created_by)
                                    <a href="{{ route('tickets.edit', $ticket) }}"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 ml-4">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endif
                            </div>

                            {{-- Attachments Section --}}
                            @if ($ticket->attachments->count() > 0)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 pb-8">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4">Attachments:
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach ($ticket->attachments as $attachment)
                                            <div class="relative group">
                                                @if (Str::contains($attachment->mime_type, 'image'))
                                                    {{-- Image Preview with aspect ratio container --}}
                                                    <div
                                                        class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-900">
                                                        <a href="{{ Storage::url($attachment->filename) }}"
                                                            target="_blank" class="block">
                                                            <img src="{{ Storage::url($attachment->filename) }}"
                                                                alt="{{ $attachment->original_filename }}"
                                                                class="object-cover hover:opacity-75 transition-opacity w-full h-full">
                                                        </a>
                                                    </div>
                                                @else
                                                    {{-- File Icon for non-images --}}
                                                    <a href="{{ Storage::url($attachment->filename) }}" target="_blank"
                                                        class="block p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow h-20">
                                                        <div class="flex items-center space-x-2">
                                                            <svg class="w-8 h-8 text-gray-500 flex-shrink-0"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                            </svg>
                                                            <span
                                                                class="text-sm text-gray-500 dark:text-gray-400 truncate flex-1">
                                                                {{ $attachment->original_filename }}
                                                            </span>
                                                        </div>
                                                    </a>
                                                @endif
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                    @php
                                                        $bytes = $attachment->size;
                                                        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
                                                        $i = $bytes ? floor(log($bytes, 1024)) : 0;
                                                        $formattedSize =
                                                            number_format($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
                                                    @endphp
                                                    {{ $formattedSize }} | {{ $attachment->mime_type }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @foreach ($ticket->replies ?? [] as $reply)
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg
                    {{ match ($reply->user->role) {
                        'admin' => 'border-l-4 border-red-500 dark:border-red-700',
                        'agent' => 'border-l-4 border-blue-500 dark:border-blue-700',
                        default => '',
                    } }}">
                        <div class="p-6">
                            <div class="flex justify-between items-start w-full">
                                <div class="flex-shrink-0">
                                    <div
                                        class="h-10 w-10 rounded-full flex items-center justify-center
                                        {{ match ($reply->user->role) {
                                            'admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'agent' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
                                        } }}">
                                        {{ substr($reply->user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-grow ms-4">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-medium text-gray-900 dark:text-gray-100">{{ $reply->user->name }}</span>
                                        <span
                                            class="text-xs px-2 py-1 rounded-full
                            {{ match ($reply->user->role) {
                                'admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                'agent' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
                            } }}">
                                            {{ ucfirst($reply->user->role) }}
                                        </span>
                                        <span
                                            class="text-sm text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                        @if ($reply->updated_at->gt($reply->created_at))
                                            <span class="text-sm text-gray-500 dark:text-gray-400">(edited)</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-gray-700 dark:text-gray-300 relative">
                                        <div id="reply-content-{{ $reply->id }}" class="pr-8">
                                            {{ $reply->content }}
                                        </div>
                                        <div class="absolute top-0 right-0 flex gap-2 items-start">
                                            @if (auth()->id() === $reply->user_id)
                                                <button onclick="toggleEdit('reply-edit-{{ $reply->id }}')"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                            @endif
                                            @if (auth()->user()->role === 'admin')
                                                <form action="{{ route('replies.destroy', $reply) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Are you sure you want to delete this reply?')"
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 flex items-start">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Reply Edit Form --}}
                                    <div id="reply-edit-{{ $reply->id }}" class="hidden mt-4">
                                        <form action="{{ route('replies.update', $reply) }}" method="POST"
                                            class="space-y-4">
                                            @csrf
                                            @method('PATCH')
                                            <x-textarea-input name="content" class="w-full"
                                                rows="4">{{ $reply->content }}</x-textarea-input>
                                            <div class="flex justify-end gap-2">
                                                <x-secondary-button type="button"
                                                    onclick="toggleEdit('reply-edit-{{ $reply->id }}')">Cancel</x-secondary-button>
                                                <x-primary-button type="submit">Save Changes</x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($ticket->status !== 'closed')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form action="{{ route('tickets.replies.store', $ticket) }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="content" :value="__('Add a reply')"
                                    class="text-gray-700 dark:text-gray-300" />
                                <x-textarea-input id="content" name="content"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300"
                                    rows="4" required />
                                <x-input-error :messages="$errors->get('content')" class="mt-2" />
                            </div>
                            <div class="flex justify-end">
                                <x-primary-button type="submit">
                                    {{ __('Post Reply') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Status Control Buttons --}}
            @if (
                ($ticket->assigned_to === auth()->id() && auth()->user()->role === 'agent') ||
                    auth()->user()->role === 'admin' ||
                    auth()->id() === $ticket->created_by)
                <div class="mt-6 flex justify-end gap-4">
                    @if ($ticket->status !== 'closed')
                        <form action="{{ route('tickets.resolve', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <x-danger-button type="submit">
                                {{ __('Close Ticket') }}
                            </x-danger-button>
                        </form>
                    @else
                        <form action="{{ route('tickets.reopen', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <x-secondary-button type="submit">
                                {{ __('Reopen Ticket') }}
                            </x-secondary-button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
    function toggleEdit(id) {
        const form = document.getElementById(id);
        // Derive content ID (assumes form ID starts with `ticket-edit` or `reply-edit-`)
        const contentId = id.replace('edit', 'content');
        const content = document.getElementById(contentId);

        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
            if (content) content.classList.add('hidden');
        } else {
            form.classList.add('hidden');
            if (content) content.classList.remove('hidden');
        }
    }
</script>
