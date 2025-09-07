<?php
// File: app/Services/GoalService.php

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

    /**
     * Get all goals for the authenticated user with calculated progress.
     *
     * @return Collection<int, \App\Models\Goal>
     */
    public function getUserGoalsWithProgress(): Collection
    {
        $goals = $this->goalRepository->getAllByUser();

        foreach ($goals as $goal) {
            $goal->progress = 0;
            if ($goal->target_amount > 0) {
                $goal->progress = ($goal->current_amount / $goal->target_amount) * 100;
            }
            // Ensure progress doesn't exceed 100% and is at least 0
            $goal->progress = max(0, min(100, $goal->progress));

            // Mark as completed if current amount meets or exceeds target
            $goal->is_completed = $goal->current_amount >= $goal->target_amount;
        }

        return $goals;
    }

    /**
     * Create a new goal.
     *
     * @param array $data
     * @return \App\Models\Goal
     */
    public function createGoal(array $data): \App\Models\Goal
    {
        // Add any business logic here before creating, e.g., initial current_amount
        return $this->goalRepository->create($data);
    }

    /**
     * Update an existing goal.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Goal
     */
    public function updateGoal(int $id, array $data): \App\Models\Goal
    {
        // Add any business logic here before updating
        $goal = $this->goalRepository->update($id, $data);
        // Automatically set is_completed based on current_amount vs target_amount
        $goal->is_completed = $goal->current_amount >= $goal->target_amount;
        $goal->save(); // Save the updated is_completed status
        return $goal;
    }

    /**
     * Delete a goal.
     *
     * @param int $id
     * @return bool
     */
    public function deleteGoal(int $id): bool
    {
        return $this->goalRepository->delete($id);
    }

    /**
     * Find a goal by ID, ensuring it belongs to the user.
     *
     * @param int $id
     * @return \App\Models\Goal
     */
    public function findById(int $id): \App\Models\Goal
    {
        return $this->goalRepository->findById($id);
    }

    /**
     * Increase the current amount of a goal.
     *
     * @param Goal $goal
     * @param float $amount
     * @return Goal
     */
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

    /**
     * Decrease the current amount of a goal.
     *
     * @param Goal $goal
     * @param float $amount
     * @return Goal
     */
    public function decreaseGoalCurrentAmount(Goal $goal, float $amount): Goal
    {
        $goal->current_amount -= $amount;
        // Ensure current_amount doesn't go below zero
        $goal->current_amount = max(0, $goal->current_amount);
        // If current amount drops below target, unmark as completed
        if ($goal->current_amount < $goal->target_amount) {
            $goal->is_completed = false;
        }
        $goal->save();
        return $goal;
    }
}
