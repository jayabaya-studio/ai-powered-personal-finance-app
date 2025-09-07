<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Services\BudgetService;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    protected $budgetService;
    protected $categoryService;

    public function __construct(BudgetService $budgetService, CategoryService $categoryService)
    {
        $this->budgetService = $budgetService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $budgets = $this->budgetService->getUserBudgetsWithCalculation();
        $categories = $this->categoryService->getCategoriesForForm();
        
        return view('user.budgets.index', compact('budgets', 'categories'));
    }

    public function store(StoreBudgetRequest $request)
    {
        $this->budgetService->createBudget($request->validated());

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }
}
