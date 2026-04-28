<div>
    <div class="text-center mb-10">
        <x-ui.heading level="1" size="4xl" class="mb-2">Your private space</x-ui.heading>
        <p class="text-gray-500 lowercase">Create a new shared home for your memories or join your partner's space.</p>
    </div>

    <div x-data="{ mode: 'create' }" class="space-y-6">
        <div class="flex p-1 bg-gray-100/50 rounded-2xl">
            <button @click="mode = 'create'" :class="mode === 'create' ? 'bg-white shadow-sm' : ''" class="flex-1 py-2 text-sm font-medium rounded-xl transition-all lowercase">Create space</button>
            <button @click="mode = 'join'" :class="mode === 'join' ? 'bg-white shadow-sm' : ''" class="flex-1 py-2 text-sm font-medium rounded-xl transition-all lowercase">Join space</button>
        </div>

        <div x-show="mode === 'create'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <x-ui.card>
                <form wire:submit.prevent="createSpace" class="space-y-6">
                    <div>
                        <x-ui.heading level="3" size="lg" class="mb-4">Give your space a name</x-ui.heading>
                        <x-ui.input wire:model="name" placeholder="John & Jane's Journey" />
                        @error('name') <p class="mt-2 text-sm text-red-500 lowercase">{{ $message }}</p> @enderror
                    </div>

                    <x-ui.button type="submit" class="w-full">Create my space</x-ui.button>
                </form>
            </x-ui.card>
        </div>

        <div x-show="mode === 'join'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <x-ui.card>
                <form wire:submit.prevent="joinSpace" class="space-y-6">
                    <div>
                        <x-ui.heading level="3" size="lg" class="mb-4">Enter invite code</x-ui.heading>
                        <x-ui.input wire:model="invite_code" placeholder="ABCDEF12" />
                        @error('invite_code') <p class="mt-2 text-sm text-red-500 lowercase">{{ $message }}</p> @enderror
                        <p class="mt-4 text-xs text-gray-400 lowercase italic">Your partner can find the code in their dashboard after creating a space.</p>
                    </div>

                    <x-ui.button type="submit" variant="secondary" class="w-full">Join space</x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>
</div>
