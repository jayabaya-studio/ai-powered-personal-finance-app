<?php

namespace App\Services;

use App\Repositories\GoalRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Goal;

class GoalService
{
    protected $goalRepository;

    public function __construct(GoalRepository $goalRepository)
    {
        $this->goalRepository = $goalRepository;
    }

    public function getUserGoalsWithProgress(): Collection
    {
        $goals = $this->goalRepository->getAllByUser();

        foreach ($goals as $goal) {
            $goal->progress = 0;
            if ($goal->target_amount > 0) {
                $goal->progress = ($goal->current_amount / $goal->target_amount) * 100;
            }
            $goal->progress = max(0, min(100, $goal->progress));

            $goal->is_completed = $goal->current_amount >= $goal->target_amount;
        }

        return $goals;
    }

    public function createGoal(array $data): \App\Models\Goal
    {
        return $this->goalRepository->create($data);
    }

    public function updateGoal(int $id, array $data): \App\Models\Goal
    {
        $goal = $this->goalRepository->update($id, $data);
        $goal->is_completed = $goal->current_amount >= $goal->target_amount;
        $goal->save();
        return $goal;
    }

    public function deleteGoal(int $id): bool
    {
        return $this->goalRepository->delete($id);
    }

    public function findById(int $id): \App\Models\Goal
    {
        return $this->goalRepository->findById($id);
    }

    public function increaseGoalCurrentAmount(Goal $goal, float $amount): Goal
    {
        $goal->current_amount += $amount;
        // Check if goal is completed
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->is_completed = true;
        }
        $goal->save();
        return $goal;
    }

    public function decreaseGoalCurrentAmount(Goal $goal, float $amount): Goal
    {
        $goal->current_amount -= $amount;
        $goal->current_amount = max(0, $goal->current_amount);
        if ($goal->current_amount < $goal->target_amount) {
            $goal->is_completed = false;
        }
        $goal->save();
        return $goal;
    }
}
