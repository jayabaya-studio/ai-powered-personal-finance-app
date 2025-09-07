@extends('layouts.app')

@section('content')
<div x-data="recurringManager()">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Recurring Transactions</h1>
        <x-button @click="openAddModal()">
            + Add Recurring
        </x-button>
    </div>

    <!-- Notifications -->
    @if (session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg relative mb-6">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg relative mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Recurring Transactions List -->
    <x-card-glass>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
                <thead>
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Description</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Next Due Date</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Frequency</th>
                        <th class="px-3 py-3.5 text-right text-sm font-semibold text-white">Amount</th>
                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($recurringTransactions as $transaction)
                        <tr>
                            <td class="py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">{{ $transaction->description }}</td>
                            <td class="px-3 py-4 text-sm text-gray-300">{{ $transaction->next_due_date->format('d M, Y') }}</td>
                            <td class="px-3 py-4 text-sm text-gray-300 capitalize">{{ $transaction->frequency }}</td>
                            <td class="px-3 py-4 text-right text-sm font-medium">
                                <span class="{{ $transaction->type === 'income' ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}{{ formatCurrency($transaction->amount) }}
                                </span>
                            </td>
                            <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                <div class="flex items-center justify-end gap-4">
                                    <button @click="openEditModal({{ json_encode($transaction) }})" class="text-yellow-400 hover:text-yellow-300">Edit</button>
                                    <button @click="openDeleteModal({{ json_encode($transaction) }})" class="text-red-400 hover:text-red-300">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-400">No recurring transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card-glass>

    <!-- Add/Edit Modal -->
    <x-modal name="recurring-form-modal" x-show="showFormModal" @close-modal.window="closeModals()">
        <x-slot name="title">
            <span x-text="formState.id ? 'Edit Recurring Transaction' : 'Add Recurring Transaction'"></span>
        </x-slot>
        {{-- [UPDATED] Including the new form partial --}}
        @include('user.recurring.partials.form-recurring', ['accounts' => $accounts, 'categories' => $categories])
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal name="delete-recurring-modal" x-show="showDeleteModal" @close-modal.window="closeModals()">
        <x-slot name="title">Confirm Delete</x-slot>
        <div class="p-6">
            <p class="text-gray-300">Are you sure you want to delete this recurring transaction: "<strong x-text="deleteState.description"></strong>"?</p>
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
function recurringManager() {
    const firstAccountId = '{{ $accounts->first()->id ?? '' }}';
    return {
        showFormModal: false,
        showDeleteModal: false,
        formState: { id: null, description: '', amount: '', start_date: '', frequency: 'monthly', type: 'expense', account_id: firstAccountId, category_id: '' },
        deleteState: { id: null, description: '', actionUrl: '' },
        
        openAddModal() {
            this.formState = { id: null, description: '', amount: '', start_date: new Date().toISOString().slice(0, 10), frequency: 'monthly', type: 'expense', account_id: firstAccountId, category_id: '' };
            this.showFormModal = true;
        },
        openEditModal(transaction) {
            this.formState = {
                id: transaction.id,
                description: transaction.description,
                amount: transaction.amount,
                start_date: transaction.start_date.slice(0, 10),
                frequency: transaction.frequency,
                type: transaction.type,
                account_id: transaction.account_id,
                category_id: transaction.category_id,
            };
            this.showFormModal = true;
        },
        openDeleteModal(transaction) {
            this.deleteState.id = transaction.id;
            this.deleteState.description = transaction.description;
            this.deleteState.actionUrl = `{{ url('recurring-transactions') }}/${transaction.id}`;
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
