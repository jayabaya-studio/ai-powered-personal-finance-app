<?php
// File: resources/views/user/transactions/partials/form-transaction.blade.php
//
// Partial for Transaction Add/Edit Form.
//
// Props:
// - $accounts: List of user accounts for dropdown.
// - $categories: List of user categories for dropdown.
// - $goals: List of user goals for dropdown. (NEW)
//
// All form inputs are bound to the `formState` object in the parent context (transactionsManager) using x-model.
//
?>
@props(['accounts', 'categories', 'goals']) {{-- Add goals to props --}}

<form :action="formState.id ? `{{ url('transactions') }}/${formState.id}` : `{{ route('transactions.store') }}`"
      :method="formState.id ? 'POST' : 'POST'" {{-- Always POST for Laravel, use @method for PUT/DELETE --}}
      class="space-y-4 p-6">
    @csrf
    <template x-if="formState.id">
        @method('PUT') {{-- Use PUT method for update operations --}}
    </template>
    <input type="hidden" name="form_type" x-bind:value="formState.id ? 'edit_transaction' : 'add_transaction'"> {{-- Hidden input to identify form for validation errors --}}


    <!-- Transaction Type -->
    <div>
        <label class="block text-sm font-medium text-gray-300">Type</label>
        <div class="mt-2 grid grid-cols-3 gap-2 rounded-lg bg-white/5 p-1">
            <button type="button" @click="formState.type = 'expense'; formState.goal_id = '';" :class="{'bg-red-500 text-white': formState.type === 'expense', 'text-gray-400 hover:bg-white/10': formState.type !== 'expense'}" class="rounded-md px-3 py-1.5 text-sm font-semibold transition-colors">Expense</button>
            <button type="button" @click="formState.type = 'income'" :class="{'bg-green-500 text-white': formState.type === 'income', 'text-gray-400 hover:bg-white/10': formState.type !== 'income'}" class="rounded-md px-3 py-1.5 text-sm font-semibold transition-colors">Income</button>
            <button type="button" @click="formState.type = 'transfer'; formState.goal_id = ''; formState.category_id = '';" :class="{'bg-blue-500 text-white': formState.type === 'transfer', 'text-gray-400 hover:bg-white/10': formState.type !== 'transfer'}" class="rounded-md px-3 py-1.5 text-sm font-semibold transition-colors">Transfer</button>
        </div>
        <input type="hidden" name="type" x-model="formState.type">
        @error('type') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Description -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
        <input type="text" name="description" id="description" required
               x-model="formState.description"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
               placeholder="Groceries, Salary, etc.">
        @error('description') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Amount -->
    <div>
        <label for="amount" class="block text-sm font-medium text-gray-300">Amount</label>
        <input type="number" name="amount" id="amount" step="0.01" required
               x-model="formState.amount"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-500 @enderror"
               placeholder="0.00">
        @error('amount') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- From Account / Account -->
    <div>
        <label for="account_id" class="block text-sm font-medium text-gray-300" x-text="formState.type === 'transfer' ? 'From Account' : 'Account'">Account</label>
        <select name="account_id" id="account_id" required
                x-model="formState.account_id"
                class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500 @error('account_id') border-red-500 @enderror">
            <option value="">Select an Account</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}">
                    {{ $account->name }} ({{ formatCurrency($account->balance) }}) {{ $account->is_joint ? '(Joint)' : '' }}
                </option>
            @endforeach
        </select>
        @error('account_id') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- To Account (for transfers) -->
    <div x-show="formState.type === 'transfer'" x-cloak>
        <label for="transfer_to_account_id" class="block text-sm font-medium text-gray-300">To Account</label>
        <select name="transfer_to_account_id" id="transfer_to_account_id"
                x-model="formState.transfer_to_account_id"
                class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500 @error('transfer_to_account_id') border-red-500 @enderror">
            <option value="">Select a Destination Account</option>
            @foreach($accounts as $account)
                {{-- Disable the same account as the source for transfers --}}
                <option value="{{ $account->id }}"
                    x-bind:disabled="formState.account_id == {{ $account->id }}">
                    {{ $account->name }} {{ $account->is_joint ? '(Joint)' : '' }}
                </option>
            @endforeach
        </select>
        @error('transfer_to_account_id') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Category (hide for transfers) -->
    <div x-show="formState.type !== 'transfer'" x-cloak>
        <label for="category_id" class="block text-sm font-medium text-gray-300">Category</label>
        <select name="category_id" id="category_id"
                x-model="formState.category_id"
                class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror">
            <option value="">Uncategorized</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Goal (show only for income transactions) -->
    <div x-show="formState.type === 'income'" x-cloak>
        <label for="goal_id" class="block text-sm font-medium text-gray-300">Associate with Goal (Optional)</label>
        <select name="goal_id" id="goal_id"
                x-model="formState.goal_id"
                class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('goal_id') border-red-500 @enderror">
            <option value="">No Goal</option>
            @foreach($goals as $goal)
                <option value="{{ $goal->id }}">{{ $goal->name }} (Target: {{ formatCurrency($goal->target_amount) }})</option>
            @endforeach
        </select>
        @error('goal_id') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Transaction Date -->
    <div>
        <label for="transaction_date" class="block text-sm font-medium text-gray-300">Date</label>
        <input type="datetime-local" name="transaction_date" id="transaction_date" required
               x-model="formState.transaction_date"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500 @error('transaction_date') border-red-500 @enderror">
        @error('transaction_date') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6 flex justify-end gap-4">
        <x-button type="button" @click="closeModals()" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
            Cancel
        </x-button>
        <x-button type="submit">
            Save Transaction
        </x-button>
    </div>
</form>
