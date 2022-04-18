<?php

declare(strict_types=1);

namespace App\Repositories;

interface LinkRepositoryInterface
{
    public function findAll(): array;

    public function findAllByUserId(int $user_id): array;

    public function findLinkById(int $id);

    public function findLinkByUrl(string $url);

    public function findLinkByCode(string $code);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function storeCode(int $id, string $code);
}