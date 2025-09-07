@extends('layouts.app')

@section('content')
<div x-data="transactionsManager()"> {{-- Removed x-init="initTransactionPage()" --}}
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Transactions</h1>
        {{-- Button to open the Add Transaction modal --}}
        <x-button @click="openAddModal()">
            + Add Transaction
        </x-button>
    </div>

    <!-- Notifications -->
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
    {{-- Display general validation errors if not tied to a specific modal --}}
    @if ($errors->any() && !session()->has('form_type'))
        <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Transaction List -->
    <x-card-glass>
        <div class="flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Description</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Category</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Account</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Goal</th> {{-- NEW: Goal column --}}
                                <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-white">Amount</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-300 sm:pl-0">{{ $transaction->transaction_date->format('d M, Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-white">{{ $transaction->description }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ optional($transaction->category)->name ?? 'Uncategorized' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">
                                        {{ $transaction->account->name }} {{ $transaction->account->is_joint ? '(Joint)' : '' }}
                                        @if($transaction->type === 'transfer' && $transaction->transferToAccount)
                                            <span class="text-gray-500 text-xs block">to {{ $transaction->transferToAccount->name }} {{ $transaction->transferToAccount->is_joint ? '(Joint)' : '' }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">
                                        {{ optional($transaction->goal)->name ?? '-' }}
                                    </td> {{-- NEW: Goal display --}}
                                    <td class="whitespace-nowrap px-3 py-4 text-right text-sm font-medium">
                                        @if($transaction->type === 'income')
                                            <span class="text-green-400">+{{ formatCurrency($transaction->amount) }}</span>
                                        @elseif($transaction->type === 'expense')
                                            <span class="text-red-400">-{{ formatCurrency($transaction->amount) }}</span>
                                        @else {{-- Transfer --}}
                                            <span class="text-blue-400">{{ formatCurrency($transaction->amount) }}</span>
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                        <div class="flex items-center justify-end gap-4">
                                            {{-- Tombol Edit memanggil `openEditModal` dan mengirim seluruh data transaksi sebagai JSON --}}
                                            <x-button type="button" @click.prevent="openEditModal({{ json_encode($transaction) }})" class="!px-3 !py-1 !text-xs !bg-gradient-to-br !from-yellow-500 !to-orange-600 hover:!from-yellow-600 hover:!to-orange-700 !shadow-yellow-500/20 hover:!shadow-yellow-500/40">
                                                Edit
                                            </x-button>
                                            {{-- Tombol Delete memanggil `openDeleteModal` --}}
                                            <x-button type="button" @click.prevent="openDeleteModal({{ json_encode($transaction) }})" class="!px-3 !py-1 !text-xs !bg-gradient-to-br !from-red-500 !to-pink-600 hover:!from-red-600 hover:!to-pink-700 !shadow-red-500/20 hover:!shadow-red-500/40">
                                                Delete
                                            </x-button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-400 sm:pl-0 text-center">
                                        No transactions found yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4 text-gray-400">{{ $transactions->links() }}</div>
                </div>
            </div>
        </div>
    </x-card-glass>

    <!-- Unified Add/Edit Transaction Modal -->
    {{-- Only ONE modal for the form. Title and action are dynamic. --}}
    <x-modal name="transaction-form-modal" x-show="showFormModal" @close-modal.window="closeModals()">
        <x-slot name="title">
            <span x-text="formState.id ? 'Edit Transaction' : 'Add New Transaction'"></span>
        </x-slot>
        {{-- The form partial will use x-model to bind to formState --}}
        @include('user.transactions.partials.form-transaction', [
            'accounts' => $accounts,
            'categories' => $categories,
            'goals' => $goals, // Pass goals to the partial
        ])
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal name="delete-confirmation-modal" x-show="showDeleteModal" @close-modal.window="closeModals()">
        <x-slot name="title">
            Confirm Delete Transaction
        </x-slot>
        <div class="p-6 text-white">
            <p>Are you sure you want to delete this transaction? <br> "<strong x-text="deleteState.description"></strong>"</p>
            <p class="text-sm text-gray-500 mt-1">This action cannot be undone.</p>
            <div class="flex justify-end gap-4 mt-6">
                <x-button type="button" @click="closeModals()" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                    Cancel
                </x-button>
                {{-- Dynamic delete form action --}}
                <form :action="deleteState.actionUrl" method="POST">
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

{{-- This is the crucial JavaScript logic that controls everything --}}
<script>
function transactionsManager() {
    const oldInput = @json(session()->getOldInput());
    const formErrors = {{ $errors->any() ? 'true' : 'false' }};
    const sessionFormType = '{{ session('form_type') ?? '' }}';
    const allTransactions = @json($transactions->items());
    const firstAccountId = '{{ $accounts->first()->id ?? '' }}';

    return {
        init() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasEditId = urlParams.has('edit_id');
            const editId = hasEditId ? parseInt(urlParams.get('edit_id')) : null;

            if (hasEditId || (formErrors && sessionFormType === 'edit_transaction')) {
                const transactionToEdit = allTransactions.find(t => t.id === editId || t.id === (formErrors && sessionFormType === 'edit_transaction' ? oldInput.id : null));
                if (transactionToEdit) {
                    this.openEditModal(transactionToEdit);
                } else if (formErrors && sessionFormType === 'edit_transaction') {
                    this.openEditModal(oldInput);
                }
            } else if (formErrors && sessionFormType === 'add_transaction') {
                this.openAddModal();
            }
        },

        showFormModal: false,
        showDeleteModal: false,

        formState: {
            id: null,
            type: 'expense',
            account_id: firstAccountId,
            category_id: '',
            amount: '',
            description: '',
            transaction_date: new Date().toISOString().slice(0, 16),
            transfer_to_account_id: null,
            goal_id: '',
        },

        deleteState: {
            id: null,
            description: '',
            actionUrl: ''
        },

        openAddModal() {
            this.formState = {
                id: null,
                type: 'expense',
                account_id: firstAccountId,
                category_id: '',
                amount: '',
                description: '',
                transaction_date: new Date().toISOString().slice(0, 16),
                transfer_to_account_id: null,
                goal_id: '',
            };

            if (formErrors && sessionFormType === 'add_transaction') {
                this.formState = {
                    ...this.formState,
                    id: oldInput.id || null,
                    type: oldInput.type || 'expense',
                    account_id: oldInput.account_id || firstAccountId,
                    category_id: oldInput.category_id || '',
                    amount: oldInput.amount || '',
                    description: oldInput.description || '',
                    transaction_date: oldInput.transaction_date || new Date().toISOString().slice(0, 16),
                    transfer_to_account_id: oldInput.transfer_to_account_id || null,
                    goal_id: oldInput.goal_id || '',
                };
            }
            this.showFormModal = true;
        },

        openEditModal(transaction) {
            this.formState = {
                id: transaction.id,
                type: transaction.type,
                account_id: transaction.account_id,
                category_id: transaction.category_id,
                amount: transaction.amount,
                description: transaction.description,
                transaction_date: (transaction.transaction_date || new Date().toISOString()).slice(0, 16),
                transfer_to_account_id: transaction.transfer_to_account_id,
                goal_id: transaction.goal_id || '',
            };

            if (formErrors && sessionFormType === 'edit_transaction' && oldInput.id == transaction.id) {
                 this.formState = {
                    ...this.formState,
                    id: oldInput.id || null,
                    type: oldInput.type || transaction.type,
                    account_id: oldInput.account_id || transaction.account_id,
                    category_id: oldInput.category_id || transaction.category_id,
                    amount: oldInput.amount || transaction.amount,
                    description: oldInput.description || transaction.description,
                    transaction_date: oldInput.transaction_date ? oldInput.transaction_date.slice(0,16) : (transaction.transaction_date ? transaction.transaction_date.slice(0,16) : new Date().toISOString().slice(0,16)),
                    transfer_to_account_id: oldInput.transfer_to_account_id || transaction.transfer_to_account_id,
                    goal_id: oldInput.goal_id || transaction.goal_id || '',
                };
            }
            this.showFormModal = true;
        },

        // Function to open 'Delete' modal
        openDeleteModal(transaction) {
            this.deleteState.id = transaction.id;
            this.deleteState.description = transaction.description;
            this.deleteState.actionUrl = `{{ url('transactions') }}/${transaction.id}`;
            this.showDeleteModal = true;
        },

        closeModals() {
            this.showFormModal = false;
            this.showDeleteModal = false;
            this.formState = {
                id: null,
                type: 'expense',
                account_id: firstAccountId,
                category_id: '',
                amount: '',
                description: '',
                transaction_date: new Date().toISOString().slice(0, 16),
                transfer_to_account_id: null,
                goal_id: '',
            };
            this.deleteState = {
                id: null,
                description: '',
                actionUrl: ''
            };

            const url = new URL(window.location.href);
            if (url.searchParams.has('edit_id')) {
                url.searchParams.delete('edit_id');
                window.history.pushState({}, '', url.toString());
            }
        }
    };
}
</script>
@endsection
