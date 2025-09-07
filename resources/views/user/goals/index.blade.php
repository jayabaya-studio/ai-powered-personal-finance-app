@extends('layouts.app')

@section('content')
<div x-data="goalManager()">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Financial Goals</h1>
        <x-button @click="openAddGoalModal()">
            + Add New Goal
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

    <!-- Goals List -->
    <x-card-glass>
        <div class="flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Target Amount</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Current Amount</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Remaining</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Target Date</th>
                                <th scope="col" class="w-1/4 px-3 py-3.5 text-left text-sm font-semibold text-white">Progress</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($goals as $goal)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">{{ $goal->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ formatCurrency($goal->target_amount) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ formatCurrency($goal->current_amount) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium {{ ($goal->target_amount - $goal->current_amount) <= 0 ? 'text-green-400' : 'text-yellow-400' }}">
                                        {{ formatCurrency($goal->target_amount - $goal->current_amount) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">
                                        {{ $goal->target_date ? $goal->target_date->format('d M, Y') : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">
                                        <div class="w-full bg-gray-700 rounded-full h-2.5">
                                            <div class="bg-purple-500 h-2.5 rounded-full" style="width: {{ min($goal->progress, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ round($goal->progress) }}%</span>
                                        @if ($goal->is_completed)
                                            <span class="ml-2 text-green-400 text-xs"> (Completed)</span>
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                        <div class="flex items-center justify-end gap-4">
                                            <x-button type="button" @click.prevent="openEditGoalModal({{ json_encode($goal) }})" class="!px-3 !py-1 !text-xs !bg-gradient-to-br !from-yellow-500 !to-orange-600 hover:!from-yellow-600 hover:!to-orange-700 !shadow-yellow-500/20 hover:!shadow-yellow-500/40">
                                                Edit
                                            </x-button>
                                            <x-button type="button" @click.prevent="openDeleteConfirmation({{ $goal->id }}, '{{ $goal->name }}')" class="!px-3 !py-1 !text-xs !bg-gradient-to-br !from-red-500 !to-pink-600 hover:!from-red-600 hover:!to-pink-700 !shadow-red-500/20 hover:!shadow-red-500/40">
                                                Delete
                                            </x-button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-400 sm:pl-0 text-center">
                                        No financial goals set yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-card-glass>

    <!-- Unified Add/Edit Goal Modal -->
    <x-modal show="showFormModal">
        <x-slot name="title">
            <span x-text="formState.id ? 'Edit Financial Goal' : 'Add New Financial Goal'"></span>
        </x-slot>
        @include('user.goals.partials.form-goal')
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal show="showDeleteConfirmation">
        <x-slot name="title">
            Confirm Delete Goal
        </x-slot>
        <div class="p-6 text-white">
            <p>Are you sure you want to delete the goal "<strong x-text="deleteState.name"></strong>"? This action cannot be undone.</p>
            <div class="flex justify-end gap-4 mt-6">
                <x-button type="button" @click="closeModals()" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                    Cancel
                </x-button>
                <form x-bind:action="deleteState.actionUrl" method="POST">
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

<script>
function goalManager() {
    const oldInput = @json(session()->getOldInput());
    const formErrors = {{ $errors->any() ? 'true' : 'false' }};
    const sessionFormType = '{{ session('form_type') ?? '' }}';
    const allGoals = @json($goals);

    return {
        showFormModal: false,
        showDeleteConfirmation: false,

        formState: {
            id: null,
            name: '',
            target_amount: '',
            current_amount: '',
            target_date: '',
            description: '',
            is_completed: false,
        },

        deleteState: {
            id: null,
            name: '',
            actionUrl: ''
        },

        init() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasEditId = urlParams.has('edit_id');
            const editId = hasEditId ? parseInt(urlParams.get('edit_id')) : null;

            if (hasEditId || (formErrors && sessionFormType === 'edit_goal')) {
                const goalToEdit = allGoals.find(g => g.id === editId || g.id === (formErrors && sessionFormType === 'edit_goal' ? oldInput.id : null));
                if (goalToEdit) {
                    this.openEditGoalModal(goalToEdit);
                } else if (formErrors && sessionFormType === 'edit_goal') {
                    this.openEditGoalModal(oldInput);
                }
            } else if (formErrors && sessionFormType === 'add_goal') {
                this.openAddGoalModal();
            }
        },

        openAddGoalModal() {
            this.formState = {
                id: null,
                name: '',
                target_amount: '',
                current_amount: '',
                target_date: '',
                description: '',
                is_completed: false,
            };

            if (formErrors && sessionFormType === 'add_goal') {
                this.formState = {
                    ...this.formState,
                    name: oldInput.name || '',
                    target_amount: oldInput.target_amount || '',
                    current_amount: oldInput.current_amount || '',
                    target_date: oldInput.target_date || '',
                    description: oldInput.description || '',
                    is_completed: oldInput.is_completed === '1' || oldInput.is_completed === true,
                };
            }
            this.showFormModal = true;
        },

        openEditGoalModal(goal) {
            this.formState = {
                id: goal.id,
                name: goal.name,
                target_amount: goal.target_amount,
                current_amount: goal.current_amount,
                target_date: goal.target_date || '',
                description: goal.description || '',
                is_completed: goal.is_completed,
            };

            if (formErrors && sessionFormType === 'edit_goal' && oldInput.id == goal.id) {
                this.formState = {
                    ...this.formState,
                    id: oldInput.id || null,
                    name: oldInput.name || goal.name,
                    target_amount: oldInput.target_amount || goal.target_amount,
                    current_amount: oldInput.current_amount || goal.current_amount,
                    target_date: oldInput.target_date || goal.target_date || '',
                    description: oldInput.description || goal.description || '',
                    is_completed: oldInput.is_completed === '1' || oldInput.is_completed === true,
                };
            }
            this.showFormModal = true;
        },

        openDeleteConfirmation(id, name) {
            this.deleteState.id = id;
            this.deleteState.name = name;
            this.deleteState.actionUrl = `{{ url('goals') }}/${id}`;
            this.showDeleteConfirmation = true;
        },

        closeModals() {
            this.showFormModal = false;
            this.showDeleteConfirmation = false;

            this.formState = {
                id: null,
                name: '',
                target_amount: '',
                current_amount: '',
                target_date: '',
                description: '',
                is_completed: false,
            };
            this.deleteState = {
                id: null,
                name: '',
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