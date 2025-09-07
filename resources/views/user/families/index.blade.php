@extends('layouts.app')

@section('content')
<div x-data="{
    showCreateModal: {{ session()->has('errors') && old('form_type') === 'create_family' ? 'true' : 'false' }},
    deleteFamilyId: null,
    deleteFamilyName: '',
    showDeleteConfirmation: false,

    openCreateFamilyModal() {
        this.showCreateModal = true;
    },
    openDeleteConfirmation(id, name) {
        this.deleteFamilyId = id;
        this.deleteFamilyName = name;
        this.showDeleteConfirmation = true;
    }
}">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Family Spaces</h1>
        <x-button @click="openCreateFamilyModal()">
            + Create Family Space
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
    @if ($errors->any() && !session()->has('form_type'))
        <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Current Active Family Space -->
    <x-card-glass class="mb-8">
        <h3 class="text-xl font-semibold text-white mb-4">Active Family Space</h3>
        @if($currentFamily)
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between bg-white/5 p-4 rounded-lg border border-white/10">
                <div class="mb-3 md:mb-0">
                    <p class="text-2xl font-bold text-purple-400">{{ $currentFamily->name }}</p>
                    <p class="text-sm text-gray-400">Owned by: {{ $currentFamily->owner->name }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('families.show', $currentFamily) }}">
                        <x-button type="button" class="!px-4 !py-2 !text-xs">View Details</x-button>
                    </a>
                    <form action="{{ route('families.clear-current') }}" method="POST">
                        @csrf
                        <x-button type="submit" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40 !px-4 !py-2 !text-xs">Clear Active</x-button>
                    </form>
                </div>
            </div>
        @else
            <p class="text-gray-400">You currently don't have an active Family Space selected.</p>
        @endif
    </x-card-glass>

    <!-- List of My Family Spaces -->
    <x-card-glass>
        <h3 class="text-xl font-semibold text-white mb-4">My Family Spaces</h3>
        <div class="space-y-4">
            @forelse ($myFamilySpaces as $family)
                <div class="bg-white/5 p-4 rounded-lg flex flex-col md:flex-row items-start md:items-center justify-between border border-white/10">
                    <div class="mb-3 md:mb-0">
                        <p class="font-semibold text-white text-lg">{{ $family->name }}</p>
                        <p class="text-gray-400 text-sm">Owner: {{ $family->owner->name }}</p>
                        @if($currentFamily && $currentFamily->id === $family->id)
                            <span class="text-purple-400 text-xs mt-1 block"> (Active)</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('families.show', $family) }}">
                            <x-button type="button" class="!px-4 !py-2 !text-xs !bg-gradient-to-br !from-blue-500 !to-indigo-600 hover:!from-blue-600 hover:!to-indigo-700 !shadow-blue-500/20 hover:!shadow-blue-500/40">
                                View
                            </x-button>
                        </a>
                        @if(!$currentFamily || $currentFamily->id !== $family->id)
                            <form action="{{ route('families.set-current', $family) }}" method="POST">
                                @csrf
                                <x-button type="submit" class="!px-4 !py-2 !text-xs">Set Active</x-button>
                            </form>
                        @endif
                        @can('delete', $family) {{-- Hanya owner yang bisa menghapus Family Space --}}
                            <x-button type="button" @click.prevent="openDeleteConfirmation({{ $family->id }}, {{ json_encode($family->name) }})" class="!px-4 !py-2 !text-xs !bg-gradient-to-br !from-red-500 !to-pink-600 hover:!from-red-600 hover:!to-pink-700 !shadow-red-500/20 hover:!shadow-red-500/40">
                                Delete
                            </x-button>
                        @endcan
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 text-lg">You are not a member of any Family Spaces. Create one or ask to be invited!</p>
            @endforelse
        </div>
    </x-card-glass>

    <!-- Modal Create Family Space -->
    <x-modal show="showCreateModal" title="Create New Family Space">
        @include('user.families.partials.form-family', ['action' => route('families.store'), 'method' => 'POST', 'form_type' => 'create_family', 'closeTrigger' => 'showCreateModal = false'])
    </x-modal>

    <!-- Modal Konfirmasi Hapus Family Space -->
    <x-modal show="showDeleteConfirmation" title="Confirm Delete Family Space">
        <div class="p-6 text-white">
            <p>Are you sure you want to delete the Family Space "<strong x-text="deleteFamilyName"></strong>"? This action cannot be undone and will affect all members and associated joint accounts.</p>
            <div class="flex justify-end gap-4 mt-6">
                <x-button type="button" @click="showDeleteConfirmation = false" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                    Cancel
                </x-button>
                <form x-bind:action="`{{ url('families') }}/${deleteFamilyId}`" method="POST">
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
