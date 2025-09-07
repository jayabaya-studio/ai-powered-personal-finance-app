@props([
    'action',
    'form_type',
    'closeTrigger' // The Alpine.js expression to close the modal
])

<form action="{{ $action }}" method="POST" class="space-y-4 p-6">
    @csrf
    <input type="hidden" name="form_type" value="{{ $form_type }}">
    <input type="hidden" name="is_joint" value="1"> {{-- Explicitly set this for joint accounts --}}

    <!-- Account Name -->
    <div>
        <label for="joint_account_name" class="block text-sm font-medium text-gray-300">Account Name</label>
        <input type="text" name="name" id="joint_account_name" required value="{{ old('name') }}"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-green-500 focus:border-green-500 @error('name') border-red-500 @enderror"
               placeholder="e.g., Family Savings">
        @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Account Type -->
    <div>
        <label for="joint_account_type" class="block text-sm font-medium text-gray-300">Account Type</label>
        <select name="type" id="joint_account_type" required
                class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-green-500 focus:border-green-500 @error('type') border-red-500 @enderror">
            <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select Type</option>
            <option value="checking" {{ old('type') == 'checking' ? 'selected' : '' }}>Checking</option>
            <option value="savings" {{ old('type') == 'savings' ? 'selected' : '' }}>Savings</option>
            <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
            <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="investment" {{ old('type') == 'investment' ? 'selected' : '' }}>Investment</option>
        </select>
        @error('type') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Initial Balance -->
    <div>
        <label for="joint_account_balance" class="block text-sm font-medium text-gray-300">Initial Balance</label>
        <input type="number" name="balance" id="joint_account_balance" step="0.01" required value="{{ old('balance', '0.00') }}"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-green-500 focus:border-green-500 @error('balance') border-red-500 @enderror"
               placeholder="0.00">
        @error('balance') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6 flex justify-end gap-4">
        <x-button type="button" @click.prevent="{{ $closeTrigger }}" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
            Cancel
        </x-button>
        <x-button type="submit">
            Create Joint Account
        </x-button>
    </div>
</form>
