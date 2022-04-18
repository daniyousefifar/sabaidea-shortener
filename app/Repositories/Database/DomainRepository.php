<?php

namespace App\Repositories\Database;

use App\Exceptions\DomainNotFoundException;
use App\Repositories\DomainRepositoryInterface;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DomainRepository implements DomainRepositoryInterface
{
    protected ContainerInterface $ci;

    public function __construct(ContainerInterface $container)
    {
        $this->ci = $container;
    }

    /**
     * Get All Domains
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findAll(): array
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `domains` ORDER BY `id` DESC");
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all domains by user id
     *
     * @param int $user_id
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findAllByUserId(int $user_id): array
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `domains` WHERE user_id = :user_id ORDER BY `id` DESC");
        $sth->execute([
            'user_id' => $user_id,
        ]);

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find Link by Domains ID
     *
     * @param int $id
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DomainNotFoundException
     */
    public function findDomainById(int $id)
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `domains` WHERE id=:id");
        $sth->execute(['id' => $id]);

        $data = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new DomainNotFoundException();
        }

        return $data;
    }

    /**
     * Find Domain by Domain
     *
     * @param string $domain
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DomainNotFoundException
     */
    public function findDomainByDomain(string $domain)
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `domains` WHERE domain=:domain");
        $sth->execute(['domain' => $domain]);

        $data = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new DomainNotFoundException();
        }

        return $data;
    }

    /**
     * Store new domain into database
     *
     * @param array $data
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(array $data): string
    {
        $db = $this->ci->get(PDO::class);

        try {
            $db->beginTransaction();

            $query = "INSERT INTO `domains` (`domain`, `user_id`) VALUES (:domain, :user_id)";
            $statement = $db->prepare($query);
            $statement->execute($data);

            $id = $db->lastInsertId();

            $db->commit();

            return $id;
        } catch (\Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Update special domain
     *
     * @param int $id
     * @param array $data
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function update(int $id, array $data)
    {
        $db = $this->ci->get(PDO::class);

        try {
            $db->beginTransaction();

            $query = "UPDATE `domains` SET domain = :domain WHERE id = :id";
            $statement = $db->prepare($query);

            $result = $statement->execute(array_merge(['id' => $id], $data));

            $db->commit();

            return $result;
        } catch (\Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Delete special domain
     *
     * @param int $id
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function delete(int $id): string
    {
        $db = $this->ci->get(PDO::class);

        try {
            $db->beginTransaction();

            $query = "DELETE FROM `domains` WHERE `id` = :id";
            $statement = $db->prepare($query);
            $result = $statement->execute(['id' => $id]);

            $db->commit();

            return $result;
        } catch (\Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
    }
}