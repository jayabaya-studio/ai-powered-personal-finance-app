@props([
    'action',
    'method' => 'POST',
    'family' => null, // The family object for editing, null for creating
    'form_type',      // 'create_family' or 'edit_family'
    'closeTrigger'    // The Alpine.js expression to close the modal, e.g., 'showCreateModal = false'
])

<form action="{{ $action }}"
      method="POST" {{-- Always POST for forms --}}
      class="space-y-4 p-6">
    @csrf
    @if($method !== 'POST')
        @method($method) {{-- Use PUT for update --}}
    @endif
    <input type="hidden" name="form_type" value="{{ $form_type }}">

    <!-- Family Name -->
    <div>
        <label for="name-{{ $form_type }}" class="block text-sm font-medium text-gray-300">Family Space Name</label>
        <input type="text" name="name" id="name-{{ $form_type }}" required
               value="{{ old('name', $family?->name) }}"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('name') border-red-500 @enderror"
               placeholder="e.g., The Johnsons' Budget">
        @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6 flex justify-end gap-4">
        <x-button type="button" @click.prevent="{{ $closeTrigger }}" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
            Cancel
        </x-button>
        <x-button type="submit">
            {{ $family ? 'Update Family Space' : 'Create Family Space' }}
        </x-button>
    </div>
</form>
