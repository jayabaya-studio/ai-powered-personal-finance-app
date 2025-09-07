@extends('layouts.app')

@section('content')
    <div x-data="{
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editingAccount: {{ isset($editAccount) ? json_encode($editAccount) : 'null' }},
        deleteAccountId: null,

        openAddModal() {
            this.editingAccount = null; // Pastikan form bersih
            this.showAddModal = true;
        },
        openEditModal(account) {
            this.editingAccount = account;
            this.showEditModal = true;
        },
        openDeleteConfirmation(accountId) {
            this.deleteAccountId = accountId;
            this.showDeleteModal = true;
        },
        cleanupState() {
            const url = new URL(window.location.href);
            if (url.searchParams.has('edit_id')) {
                url.searchParams.delete('edit_id');
                window.history.pushState({}, '', url.toString());
            }
            this.editingAccount = null;
            this.deleteAccountId = null;
        }
    }" x-init="
        @if (session()->has('errors') && session('form_type') === 'add_account') $nextTick(() => showAddModal = true) @endif
        @if (isset($editAccount) || (session()->has('errors') && session('form_type') === 'edit_account')) $nextTick(() => showEditModal = true) @endif
        $watch('showEditModal', value => { if (!value) cleanupState() });
        $watch('showDeleteModal', value => { if (!value) cleanupState() });
    ">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white">Your Accounts</h1>
            <x-button @click="openAddModal()">
                + Add Account
            </x-button>
        </div>

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
                class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg relative mb-6">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
                class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg relative mb-6">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        {{-- Tampilkan error validasi umum jika tidak terkait dengan modal spesifik --}}
        @if ($errors->any() && !session()->has('form_type')) {{-- Gunakan session()->has untuk cek eksistensi form_type --}}
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg relative mb-6" role="alert">
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($accounts as $account)
                <x-card-glass class="relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-purple-700/10 opacity-30 blur-md"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <h3 class="text-xl font-semibold text-white mb-2">{{ $account->name }}</h3>
                        <p class="text-gray-400 text-sm mb-4">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</p>
                        <p class="text-4xl font-bold text-green-400 mt-auto">{{ formatCurrency($account->balance) }}</p>

                        <div class="flex justify-end gap-3 mt-4">
                            <x-button type="button" @click.prevent="openEditModal({{ $account }})" class="!bg-gradient-to-br !from-yellow-500 !to-orange-600 hover:!from-yellow-600 hover:!to-orange-700 !shadow-yellow-500/20 hover:!shadow-yellow-500/40">
                                Edit
                            </x-button>
                            <x-button type="button" @click.prevent="openDeleteConfirmation({{ $account->id }})" class="!bg-gradient-to-br !from-red-500 !to-pink-600 hover:!from-red-600 hover:!to-pink-700 !shadow-red-500/20 hover:!shadow-red-500/40">
                                Hapus
                            </x-button>
                        </div>
                    </div>
                </x-card-glass>
            @empty
                <x-card-glass class="md:col-span-3 text-center py-10">
                    <p class="text-gray-400 text-lg">You don't have any accounts yet. Click "+ Add Account" to get started!</p>
                </x-card-glass>
            @endforelse
        </div>

        <x-modal show="showAddModal" title="Add New Account">
            <form action="{{ route('accounts.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="form_type" value="add_account">
                <div class="mb-4">
                    <label for="name" class="block text-gray-300 text-sm font-bold mb-2">Account Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                           class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="My Savings Account">
                    @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="type" class="block text-gray-300 text-sm font-bold mb-2">Account Type</label>
                    <select name="type" id="type" required
                            class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        <option value="checking" {{ old('type') == 'checking' ? 'selected' : '' }}>Checking</option>
                        <option value="savings" {{ old('type') == 'savings' ? 'selected' : '' }}>Savings</option>
                        <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="investment" {{ old('type') == 'investment' ? 'selected' : '' }}>Investment</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label for="balance" class="block text-gray-300 text-sm font-bold mb-2">Initial Balance (USD)</label>
                    <input type="number" name="balance" id="balance" step="0.01" required value="{{ old('balance', '0.00') }}"
                           class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('balance') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('balance') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-4">
                    <x-button type="button" @click="showAddModal = false" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                        Cancel
                    </x-button>
                    <x-button type="submit">
                        Save Account
                    </x-button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Edit Rekening -->
        {{-- Menambahkan form_type hidden input untuk identifikasi form --}}
        <x-modal show="showEditModal" title="Edit Account">
            <form x-bind:action="`{{ route('accounts.update', '') }}/${editingAccount ? editingAccount.id : ''}`" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit_account">
                <div class="mb-4">
                    <label for="edit_name" class="block text-gray-300 text-sm font-bold mb-2">Account Name</label>
                    <input type="text" name="name" x-bind:value="editingAccount ? editingAccount.name : '{{ old('name', '') }}'" id="edit_name" required
                           class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="edit_type" class="block text-gray-300 text-sm font-bold mb-2">Account Type</label>
                    <select name="type" id="edit_type" x-bind:value="editingAccount ? editingAccount.type : '{{ old('type', '') }}'" required
                            class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="checking">Checking</option>
                        <option value="savings">Savings</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="cash">Cash</option>
                        <option value="investment">Investment</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label for="edit_balance" class="block text-gray-300 text-sm font-bold mb-2">Balance (USD)</label>
                    <input type="number" name="balance" x-bind:value="editingAccount ? editingAccount.balance : '{{ old('balance', '') }}'" id="edit_balance" step="0.01" required
                           class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('balance') border-red-500 @enderror">
                    @error('balance') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-4">
                    <x-button type="button" @click="showEditModal = false" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                        Cancel
                    </x-button>
                    <x-button type="submit">
                        Update Account
                    </x-button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Konfirmasi Hapus -->
        <x-modal show="showDeleteModal" title="Confirm Account Deletion">
            <div class="p-6 text-white">
                <p>Are you sure you want to delete this account? This action cannot be undone and all transactions associated with this account will also be deleted.</p>
                <div class="flex justify-end gap-4 mt-6">
                    <x-button type="button" @click="showDeleteModal = false" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                        Cancel
                    </x-button>
                    <form x-bind:action="`{{ route('accounts.destroy', '') }}/${deleteAccountId}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" class="!bg-red-500 hover:!bg-red-600 !shadow-red-500/20 hover:!shadow-red-500/40">
                            Delete
                        </x-button>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>

@endsection
