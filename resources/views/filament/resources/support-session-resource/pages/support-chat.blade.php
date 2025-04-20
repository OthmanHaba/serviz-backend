<div class="flex flex-col">
    <div class="flex-1 overflow-y-auto">
        <div class="p-4 space-y-4">
            @foreach ($this->record->supportMessages as $msg)
                <div class="flex items-start">
                    <div
                        class="flex-1 {{ $msg->sender->role === 'admin' ? 'ml-auto' : '' }}">

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $msg->message }}</p>
                            <small class="text-xs text-gray-500 dark:text-gray-400">{{ $msg->created_at->diffForHumans() }}</small>
                            <strong class="text-xs text-blue-200 dark:text-blue-400">from : {{$msg->sender->name }}</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="flex items-center p-4 bg-gray-100 dark:bg-gray-900">
        <x-filament::input wire:model="newMessage" placeholder="Type a message..."
                           class="w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border rounded-md focus:outline-none focus:ring focus:border-blue-300 dark:focus:border-blue-500"/>
        <x-filament::button wire:click="sendNewMessage" class="ml-4">Send</x-filament::button>
    </div>
    <x-filament::button wire:click="setIsClosed" class="mt-4" color="danger">Close Support</x-filament::button>
</div>
