@extends('layouts.app')

@section('content')
<div x-data="budgetManager()">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Monthly Budgets</h1>
        <x-button @click="openAddModal()">
            + Add Budget
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

    <!-- Budget List -->
    <x-card-glass>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
                <thead>
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Category</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Amount</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Spent</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Remaining</th>
                        <th scope="col" class="w-1/4 px-3 py-3.5 text-left text-sm font-semibold text-white">Progress</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($budgets as $budget)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">{{ $budget->category->name }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ formatCurrency($budget->amount) }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ formatCurrency($budget->spent) }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium {{ $budget->remaining >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ formatCurrency($budget->remaining) }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">
                                <div class="w-full bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ min($budget->progress, 100) }}%"></div>
                                </div>
                                <span class="text-xs">{{ round($budget->progress) }}%</span>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                {{-- Actions can be added here later --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="whitespace-nowrap py-4 text-center text-gray-400">
                                No budgets set for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card-glass>

    <!-- [FIXED] Modal for Add Budget -->
    <x-modal x-show="showAddModal" title="Add New Budget">
        <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4 p-6">
            @csrf
            <input type="hidden" name="form_type" value="add_budget">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-300">Category</label>
                <select name="category_id" id="category_id" required x-model="formState.category_id" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-300">Budget Amount</label>
                <input type="number" name="amount" id="amount" step="0.01" required x-model="formState.amount" class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mt-6 flex justify-end gap-4">
                <x-button type="button" @click="showAddModal = false" class="!bg-gray-600">
                    Cancel
                </x-button>
                <x-button type="submit">
                    Save Budget
                </x-button>
            </div>
        </form>
    </x-modal>
</div>

<script>
function budgetManager() {
    const oldInput = @json(session()->getOldInput());
    const formErrors = {{ $errors->any() ? 'true' : 'false' }};
    const sessionFormType = '{{ session('form_type') ?? '' }}';

    return {
        showAddModal: false,
        formState: {
            category_id: '',
            amount: '',
        },

        init() {
            if (formErrors && sessionFormType === 'add_budget') {
                this.formState.category_id = oldInput.category_id || '';
                this.formState.amount = oldInput.amount || '';
                this.showAddModal = true;
            }
        },

        openAddModal() {
            this.formState.category_id = '';
            this.formState.amount = '';
            this.showAddModal = true;
        },

        handleModalClose(event) {
            if (event.detail === 'add-budget-modal') {
                this.formState.category_id = '';
                this.formState.amount = '';
            }
        },
    };
}
</script>
@endsection
