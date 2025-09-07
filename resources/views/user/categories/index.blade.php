@extends('layouts.app')

@section('content')
    @php
        $initialEditData = null;
        // Check if we are in an "edit" state, either by validation error or direct load
        if (session()->has('errors') && old('form_type') === 'edit_category') {
            // If validation failed, the "old" data is the source of truth
            $initialEditData = (object)[
                'id' => old('category_id'),
                'name' => old('name'),
                'type' => old('type'),
                'icon' => old('icon'),
                'parent_id' => old('parent_id'),
            ];
        } elseif (isset($editCategory)) {
            // Otherwise, the data from the controller is the source of truth
            $initialEditData = $editCategory;
        }
    @endphp
    <div x-data="{
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editingCategory: {{ $initialEditData ? json_encode($initialEditData) : 'null' }},
        deleteCategoryId: null,

        openAddModal() {
            this.editingCategory = null; // Clear previous edit data
            this.showAddModal = true;
        },
        openEditModal(category) {
            this.editingCategory = JSON.parse(JSON.stringify(category)); // Clone to avoid live updates
            this.showEditModal = true;
        },
        openDeleteConfirmation(categoryId) {
            this.deleteCategoryId = categoryId;
            this.showDeleteModal = true;
        },
        cleanupState() {
            // Clean up URL parameter 'edit_id' so the modal doesn't reopen on refresh
            const url = new URL(window.location.href);
            if (url.searchParams.has('edit_id')) {
                url.searchParams.delete('edit_id');
                window.history.pushState({}, '', url.toString());
            }
            // Reset state when the modal is closed
            this.editingCategory = null;
            this.deleteCategoryId = null;
        }
    }"
    x-init="
        // Open the correct modal on page load if there are validation errors
        @if (session()->has('errors') && old('form_type') === 'add_category') $nextTick(() => showAddModal = true) @endif
        @if ((isset($editCategory) && !$errors->any()) || (session()->has('errors') && old('form_type') === 'edit_category')) $nextTick(() => showEditModal = true) @endif

        // Watch for modals closing and clean up the state
        $watch('showEditModal', value => { if (!value) cleanupState() });
        $watch('showDeleteModal', value => { if (!value) cleanupState() });
    ">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white">Category List</h1>
            <x-button @click="openAddModal()">
                + Add Category
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category List Column -->
            <div class="md:col-span-2">
                <x-card-glass>
                    <h3 class="text-xl font-semibold text-white mb-4">Your Categories</h3>
                    <div class="space-y-4">
                        @forelse ($categories as $category)
                            <div class="bg-white/5 p-4 rounded-lg flex justify-between items-center border border-white/10">
                                <div>
                                    <p class="font-semibold text-white text-lg">{{ $category->name }} ({{ Str::title($category->type) }})</p>
                                    @if($category->icon)
                                        <span class="text-gray-400 text-sm">Icon: {{ $category->icon }}</span>
                                    @endif
                                    @if($category->children->isNotEmpty())
                                        <div class="pl-4 mt-2 space-y-2 border-l border-white/20">
                                            @foreach($category->children as $child)
                                                <div class="text-sm text-gray-300 flex justify-between items-center py-1">
                                                    <span>-- {{ $child->name }} ({{ Str::title($child->type) }})</span>
                                                    <!-- Actions for Sub-category -->
                                                    <div class="flex items-center gap-3">
                                                        <x-button type="button" @click.prevent="openEditModal({{ json_encode($child) }})" class="!px-3 !py-1 !text-xs !bg-gradient-to-br !from-yellow-500 !to-orange-600 hover:!from-yellow-600 hover:!to-orange-700 !shadow-yellow-500/20 hover:!shadow-yellow-500/40">
                                                            Edit
                                                        </x-button>
                                                        <x-button type="button" @click.prevent="openDeleteConfirmation({{ $child->id }})" class="!px-3 !py-1 !text-xs !bg-gradient-to-br !from-red-500 !to-pink-600 hover:!from-red-600 hover:!to-pink-700 !shadow-red-500/20 hover:!shadow-red-500/40">
                                                            Delete
                                                        </x-button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <!-- Actions for Main Category -->
                                <div class="flex items-center gap-3">
                                    <x-button type="button" @click.prevent="openEditModal({{ json_encode($category) }})" class="!px-4 !py-2 !text-xs !bg-gradient-to-br !from-yellow-500 !to-orange-600 hover:!from-yellow-600 hover:!to-orange-700 !shadow-yellow-500/20 hover:!shadow-yellow-500/40">
                                        Edit
                                    </x-button>
                                    <x-button type="button" @click.prevent="openDeleteConfirmation({{ $category->id }})" class="!px-4 !py-2 !text-xs !bg-gradient-to-br !from-red-500 !to-pink-600 hover:!from-red-600 hover:!to-pink-700 !shadow-red-500/20 hover:!shadow-red-500/40">
                                        Delete
                                    </x-button>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-lg">No categories found. Click "+ Add Category" to get started!</p>
                        @endforelse
                    </div>
                </x-card-glass>
            </div>
        </div>

        <!-- Add Category Modal -->
        <x-modal show="showAddModal" title="Add New Category">
            <form action="{{ route('categories.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="form_type" value="add_category">
                <div class="mb-4">
                    <label for="name" class="block text-gray-300 text-sm font-bold mb-2">Category Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                           class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Food">
                    @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="type" class="block text-gray-300 text-sm font-bold mb-2">Category Type</label>
                    <select name="type" id="type" required
                            class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                        <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="icon" class="block text-gray-300 text-sm font-bold mb-2">Icon (Optional, e.g., fas fa-utensils)</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                           class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('icon') border-red-500 @enderror"
                           placeholder="fa-food">
                    @error('icon') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label for="parent_id" class="block text-gray-300 text-sm font-bold mb-2">Parent Category (Optional)</label>
                    <select name="parent_id" id="parent_id"
                            class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500 @error('parent_id') border-red-500 @enderror">
                        <option value="">None (Main Category)</option>
                        @foreach($allCategoriesForForm as $cat)
                            <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>{{ $cat->display_name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-4">
                    <x-button type="button" @click="showAddModal = false" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                        Cancel
                    </x-button>
                    <x-button type="submit">
                        Save Category
                    </x-button>
                </div>
            </form>
        </x-modal>

        <!-- Edit Category Modal -->
        <x-modal show="showEditModal" title="Edit Category">
            <form x-show="editingCategory" x-bind:action="`{{ route('categories.update', '') }}/${editingCategory ? editingCategory.id : ''}`" method="POST" class="p-6" style="display: none;">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit_category">
                <input type="hidden" name="category_id" x-bind:value="editingCategory ? editingCategory.id : ''">
                <div class="mb-4">
                    <label for="edit_name" class="block text-gray-300 text-sm font-bold mb-2">Category Name</label>
                    <input type="text" name="name" id="edit_name" required x-model="editingCategory.name"
                           class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label for="edit_type" class="block text-gray-300 text-sm font-bold mb-2">Category Type</label>
                    <select name="type" id="edit_type" required x-model="editingCategory.type" class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                    </select>
                    @error('type')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label for="edit_icon" class="block text-gray-300 text-sm font-bold mb-2">Icon (Optional, e.g., fas fa-utensils)</label>
                    <input type="text" name="icon" id="edit_icon" x-model="editingCategory.icon"
                           class="shadow-sm bg-white/5 border border-white/10 rounded-lg w-full py-2 px-3 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('icon') border-red-500 @enderror"
                           placeholder="fa-food">
                    @error('icon')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-6">
                    <label for="edit_parent_id" class="block text-gray-300 text-sm font-bold mb-2">Parent Category (Optional)</label>
                    <select name="parent_id" id="edit_parent_id" x-model="editingCategory.parent_id"
                            class="mt-1 block w-full bg-white/5 border-white/10 rounded-md shadow-sm text-white focus:ring-blue-500 focus:border-blue-500 @error('parent_id') border-red-500 @enderror">
                        <option value="">None (Main Category)</option>
                        @foreach($allCategoriesForForm as $cat)
                            <option value="{{ $cat->id }}" x-bind:disabled="editingCategory && {{ $cat->id }} === editingCategory.id">{{ $cat->display_name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end gap-4">
                    <x-button type="button" @click="showEditModal = false" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                        Cancel
                    </x-button>
                    <x-button type="submit">
                        Update Category
                    </x-button>
                </div>
            </form>
        </x-modal>

        <!-- Delete Confirmation Modal -->
        <x-modal show="showDeleteModal" title="Confirm Delete Category">
            <div class="p-6 text-white">
                <p>Are you sure you want to delete this category? Deleting a parent category will also delete all its sub-categories. This action cannot be undone!</p>
                <div class="flex justify-end gap-4 mt-6">
                    <x-button type="button" @click="showDeleteModal = false" class="!bg-gray-600 hover:!bg-gray-700 !shadow-gray-500/20 hover:!shadow-gray-500/40">
                        Cancel
                    </x-button>
                    <form x-bind:action="`{{ route('categories.destroy', '') }}/${deleteCategoryId}`" method="POST">
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
