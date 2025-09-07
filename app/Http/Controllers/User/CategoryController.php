<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $categories = $this->categoryService->getUserCategories();
        $allCategoriesForForm = $this->categoryService->getCategoriesForForm();

        $editCategory = null;
        $editId = $request->query('edit_id');

        if ($request->session()->has('errors') && $request->old('form_type') === 'edit_category') {
            $editId = $request->old('category_id', $editId);
        }

        if ($editId) {
            try {
                $editCategory = $this->categoryService->findById((int)$editId);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
                return redirect()->route('categories.index')->with('error', 'Category not found or not authorized.');
            }
        }

        return view('user.categories.index', compact('categories', 'editCategory', 'allCategoriesForForm'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->createCategory($request->validated());
        return redirect()->route('categories.index')->with('success', 'Category added successfully!');
    }

    public function edit(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403, 'This action is unauthorized.');
        }
        return redirect()->route('categories.index', ['edit_id' => $category->id]);
    }

    public function update(UpdateCategoryRequest $request, Category $category) // Ganti StoreCategoryRequest dengan UpdateCategoryRequest
    {
        if ($category->user_id !== auth()->id()) {
            abort(403, 'This action is unauthorized.');
        }

        $this->categoryService->updateCategory($category->id, $request->validated());
        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403, 'This action is unauthorized.');
        }
        
        $this->categoryService->deleteCategory($category->id);
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}