@extends('layouts.app')

@section('content')
<div x-data="familyShowManager()">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Family Space: {{ $family->name }}</h1>
        <div class="flex gap-4">
            @can('update', $family)
                <x-button @click="openEditFamilyModal()" class="!bg-gradient-to-br !from-yellow-500 !to-orange-600 hover:!from-yellow-600 hover:!to-orange-700 !shadow-yellow-500/20 hover:!shadow-yellow-500/40">
                    Edit Family
                </x-button>
            @endcan
            @can('addMember', $family)
                <x-button @click="openInviteMemberModal()">
                    + Invite Member
                </x-button>
            @endcan
            @can('createJointAccount', $family)
                <x-button @click="openCreateJointAccountModal()" class="!bg-gradient-to-br !from-green-500 !to-emerald-600 hover:!from-green-600 hover:!to-emerald-700 !shadow-green-500/20 hover:!shadow-green-500/40">
                    + Joint Account
                </x-button>
            @endcan
        </div>
    </div>

    <!-- Notifications & Errors -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
            class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg relative mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
     @if ($errors->any() && !old('form_type'))
        <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">There were some problems with your input.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <x-card-glass>
                <h3 class="text-xl font-semibold text-white mb-4">Members ({{ $family->members->count() }})</h3>
                <div class="space-y-3">
                    @forelse ($family->members as $member)
                        <div class="flex items-center justify-between bg-white/5 p-3 rounded-lg border border-white/10">
                            <div>
                                <p class="font-semibold text-white">{{ $member->name }}</p>
                                <p class="text-sm text-gray-400">{{ $member->email }}</p>
                                @if($family->owner_user_id === $member->id)
                                    <span class="text-purple-400 text-xs font-semibold">(Owner)</span>
                                @elseif($member->pivot->role === 'admin')
                                    <span class="text-cyan-400 text-xs font-semibold">(Admin)</span>
                                @else
                                    <span class="text-blue-400 text-xs font-semibold">({{ ucfirst($member->pivot->role) }})</span>
                                @endif
                            </div>
                            @can('removeMember', [$family, $member])
                                <x-button type="button" @click.prevent="openRemoveMemberConfirmation({{ $member->id }}, {{ json_encode($member->name) }})" class="!px-3 !py-1 !text-xs !bg-red-500 hover:!bg-red-600 !shadow-red-500/20 hover:!shadow-red-500/40">
                                    Remove
                                </x-button>
                            @endcan
                        </div>
                    @empty
                        <p class="text-gray-400">No members yet, invite someone!</p>
                    @endforelse
                </div>
            </x-card-glass>
        </div>

        <div>
            <x-card-glass>
                <h3 class="text-xl font-semibold text-white mb-4">Joint Accounts ({{ $jointAccounts->count() }})</h3>
                <div class="space-y-3">
                    @forelse ($jointAccounts as $account)
                        <div class="flex items-center justify-between bg-white/5 p-3 rounded-lg border border-white/10">
                            <div>
                                <p class="font-semibold text-white">{{ $account->name }}</p>
                                <p class="text-sm text-gray-400">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</p>
                            </div>
                             <p class="text-lg font-bold text-green-400">{{ formatCurrency($account->balance) }}</p>
                        </div>
                    @empty
                        <p class="text-gray-400">No joint accounts yet. Create one!</p>
                    @endforelse
                </div>
            </x-card-glass>
        </div>
    </div>

    <!-- Modals -->
    <x-modal x-show="showEditModal" @close="showEditModal = false" title="Edit Family Space">
        @include('user.families.partials.form-family', ['action' => route('families.update', $family), 'method' => 'PUT', 'family' => $family, 'form_type' => 'edit_family', 'closeTrigger' => 'showEditModal = false'])
    </x-modal>

    <x-modal x-show="showInviteModal" @close="showInviteModal = false" title="Invite Member to {{ $family->name }}">
        @include('user.families.partials.form-invite-member', ['action' => route('families.invite-member', $family), 'family' => $family, 'closeTrigger' => 'showInviteModal = false'])
    </x-modal>

    <x-modal x-show="showRemoveMemberConfirmation" @close="showRemoveMemberConfirmation = false" title="Confirm Remove Member">
        <div class="p-6 text-white">
            <p>Are you sure you want to remove "<strong x-text="removeMemberName"></strong>" from {{ $family->name }}?</p>
            <div class="mt-6 flex justify-end gap-4">
                <x-button type="button" @click="showRemoveMemberConfirmation = false" class="!bg-gray-600 hover:!bg-gray-700">
                    Cancel
                </x-button>
                <form x-bind:action="removeMemberBaseUrl.replace('MEMBER_ID_PLACEHOLDER', removeMemberId)" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" class="!bg-red-500 hover:!bg-red-600">
                        Remove
                    </x-button>
                </form>
            </div>
        </div>
    </x-modal>

    <x-modal x-show="showCreateJointAccountModal" @close="showCreateJointAccountModal = false" title="Create Joint Account">
        @include('user.families.partials.form-joint-account', ['action' => route('families.joint-accounts.store', $family), 'form_type' => 'create_joint_account', 'closeTrigger' => 'showCreateJointAccountModal = false'])
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
function familyShowManager() {
    const errors = @json($errors->isNotEmpty());
    const oldFormType = @json(old('form_type'));

    return {
        showEditModal: errors && oldFormType === 'edit_family',
        showInviteModal: errors && oldFormType === 'invite_member',
        showCreateJointAccountModal: errors && oldFormType === 'create_joint_account',
        showRemoveMemberConfirmation: false,

        removeMemberId: null,
        removeMemberName: '',
        removeMemberBaseUrl: @json(route('families.remove-member', ['family' => $family, 'member' => 'MEMBER_ID_PLACEHOLDER'])),

        openEditFamilyModal() {
            this.showEditModal = true;
        },
        openInviteMemberModal() {
            this.showInviteModal = true;
        },
        openCreateJointAccountModal() {
            this.showCreateJointAccountModal = true;
        },
        openRemoveMemberConfirmation(memberId, memberName) {
            this.removeMemberId = memberId;
            this.removeMemberName = memberName;
            this.showRemoveMemberConfirmation = true;
        }
    };
}
</script>
@endpush
