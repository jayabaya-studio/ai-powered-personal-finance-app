<?php
namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Support\Collection;
use App\Models\Category;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getUserCategories(): Collection
    {
        return $this->categoryRepository->getAllByUser();
    }

    public function getCategoriesForForm(): Collection
    {
        $allUserCategories = Category::where('user_id', auth()->id())->orderBy('name')->get();
        $formattedCategories = collect();

        $this->buildCategoryTree($allUserCategories, $formattedCategories);
        
        return $formattedCategories;
    }

    protected function buildCategoryTree(Collection $categories, Collection $formattedCategories, $parentId = null, $prefix = '')
    {
        $filteredCategories = $categories->where('parent_id', $parentId);

        foreach ($filteredCategories as $category) {
            $category->display_name = $prefix . $category->name;
            $formattedCategories->push($category);
            $this->buildCategoryTree($categories, $formattedCategories, $category->id, $prefix . '-- ');
        }
    }


    public function createCategory(array $data)
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, array $data)
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id)
    {
        return $this->categoryRepository->delete($id);
    }

    public function findById(int $id): Category
    {
        return $this->categoryRepository->findById($id);
    }
}