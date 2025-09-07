<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoalRequest;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    protected $goalService;

    public function __construct(GoalService $goalService)
    {
        $this->goalService = $goalService;
    }

    public function index(Request $request)
    {
        $goals = $this->goalService->getUserGoalsWithProgress();
        $editGoal = null;

        if ($request->has('edit_id')) {
            try {
                $editGoal = $this->goalService->findById((int)$request->edit_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return redirect()->route('goals.index')->with('error', 'Goal not found or unauthorized.');
            }
        }
        
        return view('user.goals.index', compact('goals', 'editGoal'));
    }

    public function store(StoreGoalRequest $request)
    {
        $this->goalService->createGoal($request->validated());
        return redirect()->route('goals.index')->with('success', 'Goal created successfully.');
    }

    public function edit(Goal $goal)
    {
        if ($goal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        return redirect()->route('goals.index', ['edit_id' => $goal->id]);
    }

    public function update(StoreGoalRequest $request, Goal $goal)
    {
        if ($goal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $this->goalService->updateGoal($goal->id, $request->validated());
        return redirect()->route('goals.index')->with('success', 'Goal updated successfully.');
    }

    public function destroy(Goal $goal)
    {
        if ($goal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $this->goalService->deleteGoal($goal->id);
        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully.');
    }
}

