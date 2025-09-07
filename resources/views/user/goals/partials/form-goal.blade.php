<?php
// File: resources/views/user/goals/partials/form-goal.blade.php

// This partial provides the form for adding or editing financial goals.
// It is designed to be included within an Alpine.js x-data context (goalManager).
// All form inputs are bound to the `formState` object in the parent context using x-model.
//
?>
<form :action="formState.id ? `{{ url('goals') }}/${formState.id}` : `{{ route('goals.store') }}`"
      :method="formState.id ? 'POST' : 'POST'" {{-- Always POST for Laravel, use @method for PUT/DELETE --}}
      class="space-y-4 p-6">
    @csrf
    <template x-if="formState.id">
        @method('PUT') {{-- Use PUT method for update operations --}}
    </template>
    <input type="hidden" name="form_type" x-bind:value="formState.id ? 'edit_goal' : 'add_goal'"> {{-- Hidden input to identify form for validation errors --}}

    <!-- Goal Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-300">Goal Name</label>
        <input type="text" name="name" id="name" required
               x-model="formState.name"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('name') border-red-500 @enderror"
               placeholder="e.g., Save for a Down Payment">
        @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Target Amount -->
    <div>
        <label for="target_amount" class="block text-sm font-medium text-gray-300">Target Amount</label>
        <input type="number" name="target_amount" id="target_amount" step="0.01" required
               x-model="formState.target_amount"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('target_amount') border-red-500 @enderror"
               placeholder="0.00">
        @error('target_amount') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Current Amount -->
    <div>
        <label for="current_amount" class="block text-sm font-medium text-gray-300">Current Amount</label>
        <input type="number" name="current_amount" id="current_amount" step="0.01"
               x-model="formState.current_amount"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('current_amount') border-red-500 @enderror"
               placeholder="0.00">
        @error('current_amount') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Target Date -->
    <div>
        <label for="target_date" class="block text-sm font-medium text-gray-300">Target Date (Optional)</label>
        <input type="date" name="target_date" id="target_date"
               x-model="formState.target_date"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('target_date') border-red-500 @enderror">
        @error('target_date') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Description -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-300">Description (Optional)</label>
        <textarea name="description" id="description" rows="3"
                  x-model="formState.description"
                  class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('description') border-red-500 @enderror"
                  placeholder="Provide more details about your goal"></textarea>
        @error('description') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Is Completed Checkbox (for editing) -->
    <template x-if="formState.id"> {{-- Only show when editing an existing goal --}}
        <div class="flex items-center mt-4">
            <input type="checkbox" name="is_completed" id="is_completed" value="1"
                   x-model="formState.is_completed"
                   class="rounded text-purple-600 shadow-sm focus:ring-purple-500 @error('is_completed') border-red-500 @enderror">
            <label for="is_completed" class="ml-2 block text-sm text-gray-300">Mark as Completed</label>
            @error('is_completed') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
        </div>
    </template>

    <div class="mt-6 flex justify-end gap-4">
        <x-button type="button" @click="closeModals()" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
            Cancel
        </x-button>
        <x-button type="submit">
            <span x-text="formState.id ? 'Update Goal' : 'Save Goal'">Save Goal</span>
        </x-button>
    </div>
</form>
