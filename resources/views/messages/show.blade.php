<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(!empty($conversations) && count($conversations) > 0)
                    @foreach($conversations as $conversation)
                        @php
                            $unreadCount = \App\Models\Message::where('sender_id', $conversation->user_id)
                                ->where('receiver_id', auth()->id())
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        
                        <a href="{{ route('messages.show', $conversation->user_id) }}" class="block border-b py-4 hover:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <div class="font-semibold {{ $unreadCount > 0 ? 'text-blue-600' : '' }}">
                                        {{ $conversation->name }}
                                        @if(!empty($conversations) && count($conversations) > 0)
                                            <span class="bg-red-500 text-black text-xs rounded-full px-2 py-1 ml-2">
                                                {{ $unreadCount }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600 truncate">
                                        {{ $conversation->last_message }}
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($conversation->last_message_time)->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <p class="text-gray-500">No messages yet.</p>
                @endif
                
            </div>
        </div>
    </div>
</x-app-layout>