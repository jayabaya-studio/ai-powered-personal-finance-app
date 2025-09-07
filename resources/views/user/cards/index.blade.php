@extends('layouts.app')

@section('content')
<div x-data="cardsManager()">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">My Cards</h1>
        <x-button @click="openAddModal()">
            + Add New Card
        </x-button>
    </div>

    <!-- Notifications -->
    @if (session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg relative mb-6">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Cards List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($cards as $card)
            <x-card-glass class="flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start">
                        <div class="font-bold text-xl italic {{ strtolower($card->card_type) === 'visa' ? 'text-blue-400' : 'text-orange-400' }}">
                            {{ $card->card_type }}
                        </div>
                        @if ($card->is_default)
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-300 rounded-full">Default</span>
                        @endif
                    </div>
                    <div class="my-8 text-center text-2xl font-mono tracking-widest text-white">
                        •••• •••• •••• {{ $card->card_number }}
                    </div>
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-gray-400">Card Holder</p>
                            <p class="font-medium text-white">{{ Auth::user()->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Expires</p>
                            <p class="font-medium text-white">{{ $card->expiry_date }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-white/10 flex justify-end gap-2">
                    @if (!$card->is_default)
                        <form action="{{ route('cards.update', $card) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_default" value="1">
                            <button type="submit" class="text-xs text-gray-300 hover:text-white">Set as Default</button>
                        </form>
                        <span class="text-gray-500">|</span>
                    @endif
                    <button @click="openDeleteModal({{ json_encode($card) }})" class="text-xs text-red-400 hover:text-red-300">Delete</button>
                </div>
            </x-card-glass>
        @empty
            <p class="text-gray-400 md:col-span-2 lg:col-span-3 text-center">You haven't added any cards yet.</p>
        @endforelse
    </div>

    <!-- Add/Edit Card Modal -->
    <x-modal name="card-form-modal" x-show="showFormModal" @close-modal.window="closeModals()">
        <x-slot name="title">
            <span x-text="formState.id ? 'Edit Card' : 'Add New Card'"></span>
        </x-slot>
        <form :action="formState.id ? `{{ url('cards') }}/${formState.id}` : `{{ route('cards.store') }}`" method="POST" class="p-6 space-y-4">
            @csrf
            <template x-if="formState.id">
                @method('PUT')
            </template>

            <div>
                <label for="card_type" class="block text-sm font-medium text-gray-300">Card Type</label>
                <select name="card_type" id="card_type" x-model="formState.card_type" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
                    <option value="VISA">VISA</option>
                    <option value="Mastercard">Mastercard</option>
                </select>
            </div>

            <div>
                <label for="card_number" class="block text-sm font-medium text-gray-300">Last 4 Digits</label>
                <input type="text" name="card_number" id="card_number" x-model="formState.card_number" placeholder="1234" maxlength="4" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="expiry_date" class="block text-sm font-medium text-gray-300">Expiry Date (MM/YY)</label>
                <input type="text" name="expiry_date" id="expiry_date" x-model="formState.expiry_date" placeholder="12/27" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-end gap-4 pt-4">
                <x-button type="button" @click="closeModals()" class="!bg-gray-600">Cancel</x-button>
                <x-button type="submit">Save Card</x-button>
            </div>
        </form>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal name="delete-card-modal" x-show="showDeleteModal" @close-modal.window="closeModals()">
        <x-slot name="title">Confirm Delete</x-slot>
        <div class="p-6">
            <p class="text-gray-300">Are you sure you want to delete the card ending in <strong x-text="deleteState.card_number"></strong>?</p>
            <div class="flex justify-end gap-4 mt-6">
                <x-button type="button" @click="closeModals()" class="!bg-gray-600">Cancel</x-button>
                <form :action="deleteState.actionUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" class="!bg-red-500">Delete</x-button>
                </form>
            </div>
        </div>
    </x-modal>
</div>

<script>
function cardsManager() {
    return {
        showFormModal: false,
        showDeleteModal: false,
        formState: {
            id: null,
            card_type: 'VISA',
            card_number: '',
            expiry_date: '',
        },
        deleteState: {
            id: null,
            card_number: '',
            actionUrl: ''
        },
        openAddModal() {
            this.formState = { id: null, card_type: 'VISA', card_number: '', expiry_date: '' };
            this.showFormModal = true;
        },
        openDeleteModal(card) {
            this.deleteState.id = card.id;
            this.deleteState.card_number = card.card_number;
            this.deleteState.actionUrl = `{{ url('cards') }}/${card.id}`;
            this.showDeleteModal = true;
        },
        closeModals() {
            this.showFormModal = false;
            this.showDeleteModal = false;
        }
    };
}
</script>
@endsection
