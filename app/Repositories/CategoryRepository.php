<?php
namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryRepository
{
    public function getAllByUser()
    {
        return Category::where('user_id', Auth::id())
                        ->with('children')
                        ->whereNull('parent_id')
                        ->orderBy('name')
                        ->get();
    }

    public function create(array $data)
    {
        $data['user_id'] = Auth::id();
        return Category::create($data);
    }

    public function findById(int $id)
    {
        return Category::where('user_id', Auth::id())->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $category = $this->findById($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id)
    {
        $category = $this->findById($id);
        return $category->delete();
    }
}