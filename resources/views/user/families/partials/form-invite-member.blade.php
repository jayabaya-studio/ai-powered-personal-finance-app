@props([
    'action',
    'family',
    'closeTrigger' // The Alpine.js expression to close the modal, e.g., 'showInviteModal = false'
])

<form action="{{ $action }}" method="POST" class="space-y-4 p-6">
    @csrf
    <input type="hidden" name="form_type" value="invite_member"> {{-- Hidden input to identify form for validation errors --}}

    <!-- Member Email -->
    <div>
        <label for="invite_email" class="block text-sm font-medium text-gray-300">Member's Email</label>
        <input type="email" name="email" id="invite_email" required value="{{ old('email') }}"
               class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('email') border-red-500 @enderror"
               placeholder="member@example.com">
        @error('email') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Role -->
    <div>
        <label for="invite_role" class="block text-sm font-medium text-gray-300">Role</label>
        <select name="role" id="invite_role"
                class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-purple-500 focus:border-purple-500 @error('role') border-red-500 @enderror">
            <option value="member" {{ old('role', 'member') == 'member' ? 'selected' : '' }}>Member</option>
            {{-- Only an owner or admin can assign the admin role, controlled by policy --}}
            @can('update', $family)
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            @endcan
        </select>
        @error('role') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6 flex justify-end gap-4">
        <x-button type="button" @click.prevent="{{ $closeTrigger }}" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
            Cancel
        </x-button>
        <x-button type="submit">
            Send Invitation
        </x-button>
    </div>
</form>
