<?php

declare(strict_types=1);

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function findAll(): array;

    public function findUserById(int $id);

    public function findUserByEmail(string $email);

    public function isUserExists(string $email);

    public function create(array $data);
}