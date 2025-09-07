<?php

namespace App\Repositories;

use App\Models\Goal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class GoalRepository
{
    public function getAllByUser(): Collection
    {
        return Goal::where('user_id', Auth::id())->get();
    }

    public function create(array $data): Goal
    {
        $data['user_id'] = Auth::id();
        return Goal::create($data);
    }

    public function findById(int $id): Goal
    {
        return Goal::where('user_id', Auth::id())->findOrFail($id);
    }

    public function update(int $id, array $data): Goal
    {
        $goal = $this->findById($id);
        $goal->update($data);
        return $goal;
    }

    public function delete(int $id): bool
    {
        $goal = $this->findById($id);
        return $goal->delete();
    }

    public function getActiveGoals(int $userId)
    {
        return Goal::where('user_id', $userId)
            ->where('is_completed', false)
            ->orderBy('target_date', 'asc')
            ->get();
    }
}

