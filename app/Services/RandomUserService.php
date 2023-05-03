<?php

namespace App\Services;

use App\Repositories\RandomUserRepository;

class RandomUserService
{
    protected $repository;

    public function __construct(RandomUserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRandomUser()
    {
        $users = $this->repository->getUsers();
        $randomUser = $users[rand(0, count($users) - 1)];
        return $randomUser;
    }

    public function getRandomTestUser()
    {
        $testUsers = $this->repository->getTestUsers();
        $randomUser = $testUsers[rand(0, count($testUsers) - 1)];
        return $randomUser;

    }
}
