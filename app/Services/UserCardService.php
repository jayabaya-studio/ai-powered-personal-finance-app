<?php

namespace App\Services;

use App\Repositories\UserCardRepository;
use Illuminate\Support\Facades\DB;

class UserCardService
{
    protected $repository;

    public function __construct(UserCardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllForUser()
    {
        return $this->repository->getAllByUser();
    }

    public function create(array $data)
    {
        // If this new card is set as default, remove the flag from all others first.
        if (isset($data['is_default']) && $data['is_default']) {
            $this->repository->removeDefaultFlagFromAll();
        }
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        DB::transaction(function () use ($id, $data) {
            // If this card is being set as default, remove the flag from all others first.
            if (isset($data['is_default']) && $data['is_default']) {
                $this->repository->removeDefaultFlagFromAll();
            }
            $this->repository->update($id, $data);
        });
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}
