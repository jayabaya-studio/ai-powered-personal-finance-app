<?php

namespace App\Services;

use App\Repositories\RecurringTransactionRepository;

class RecurringTransactionService
{
    protected $repository;

    public function __construct(RecurringTransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllForUser()
    {
        return $this->repository->getAllByUser();
    }

    public function create(array $data)
    {
        // Business logic for creating a recurring transaction will go here
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        // Business logic for updating will go here
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        // Business logic for deleting will go here
        return $this->repository->delete($id);
    }
}
