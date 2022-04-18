<?php

declare(strict_types=1);

namespace App\Repositories;

interface DomainRepositoryInterface
{
    public function findAll(): array;

    public function findAllByUserId(int $user_id): array;

    public function findDomainById(int $id);

    public function findDomainByDomain(string $domain);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);
}