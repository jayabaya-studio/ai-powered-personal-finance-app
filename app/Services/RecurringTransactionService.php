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
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}
