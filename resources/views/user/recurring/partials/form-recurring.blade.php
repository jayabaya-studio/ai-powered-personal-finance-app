@props(['accounts', 'categories'])

<form :action="formState.id ? `{{ url('recurring-transactions') }}/${formState.id}` : `{{ route('recurring-transactions.store') }}`" method="POST" class="p-6 space-y-4">
    @csrf
    <template x-if="formState.id">
        @method('PUT')
    </template>

    <!-- Transaction Type -->
    <div>
        <label class="block text-sm font-medium text-gray-300">Type</label>
        <div class="mt-2 grid grid-cols-2 gap-2 rounded-lg bg-white/5 p-1">
            <button type="button" @click="formState.type = 'expense'" :class="{'bg-red-500 text-white': formState.type === 'expense', 'text-gray-400 hover:bg-white/10': formState.type !== 'expense'}" class="rounded-md px-3 py-1.5 text-sm font-semibold transition-colors">Expense</button>
            <button type="button" @click="formState.type = 'income'" :class="{'bg-green-500 text-white': formState.type === 'income', 'text-gray-400 hover:bg-white/10': formState.type !== 'income'}" class="rounded-md px-3 py-1.5 text-sm font-semibold transition-colors">Income</button>
        </div>
        <input type="hidden" name="type" x-model="formState.type">
    </div>

    <!-- Description -->
    <div>
        <label for="recurring_description" class="block text-sm font-medium text-gray-300">Description</label>
        <input type="text" name="description" id="recurring_description" required x-model="formState.description" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Netflix Subscription">
    </div>

    <!-- Amount -->
    <div>
        <label for="recurring_amount" class="block text-sm font-medium text-gray-300">Amount</label>
        <input type="number" name="amount" id="recurring_amount" step="0.01" required x-model="formState.amount" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
    </div>

    <!-- Account -->
    <div>
        <label for="recurring_account_id" class="block text-sm font-medium text-gray-300">Account</label>
        <select name="account_id" id="recurring_account_id" required x-model="formState.account_id" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
            <option value="">Select an Account</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Category -->
    <div>
        <label for="recurring_category_id" class="block text-sm font-medium text-gray-300">Category</label>
        <select name="category_id" id="recurring_category_id" x-model="formState.category_id" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
            <option value="">Uncategorized</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Frequency & Start Date -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="recurring_frequency" class="block text-sm font-medium text-gray-300">Frequency</label>
            <select name="frequency" id="recurring_frequency" required x-model="formState.frequency" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-300">Start Date</label>
            <input type="date" name="start_date" id="start_date" required x-model="formState.start_date" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    
    <div class="flex justify-end gap-4 pt-4">
        <x-button type="button" @click="closeModals()" class="!bg-gray-600">Cancel</x-button>
        <x-button type="submit">Save</x-button>
    </div>
</form>
