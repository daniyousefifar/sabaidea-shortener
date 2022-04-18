<?php

namespace App\Repositories\Database;

use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserRepository implements UserRepositoryInterface
{
    protected ContainerInterface $ci;

    public function __construct(ContainerInterface $container)
    {
        $this->ci = $container;
    }

    /**
     * Get All Users
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findAll(): array
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM users ORDER BY id DESC");
        $sth->execute();

        $data = $sth->fetchAll(PDO::FETCH_ASSOC);

        array_walk($data, function (&$a) {
            unset($a['password']);
        });

        return $data;
    }

    /**
     * Find User by User ID
     *
     * @param int $id
     * @return mixed
     * @throws UserNotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findUserById(int $id)
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM users WHERE id=:id");
        $sth->execute(['id' => $id]);

        $data = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new UserNotFoundException();
        }

        unset($data['password']);

        return $data;
    }

    /**
     * Find User by Email Address
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws UserNotFoundException
     */
    public function findUserByEmail(string $email)
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM users WHERE email=:email");
        $sth->execute(['email' => $email]);

        $data = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new UserNotFoundException();
        }

        return $data;
    }

    public function isUserExists(string $email): bool
    {
        try {
            $this->findUserByEmail($email);

            return true;
        } catch (UserNotFoundException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return false;
        }
    }

    public function create(array $data): string
    {
        $db = $this->ci->get(PDO::class);

        try {
            $db->beginTransaction();

            $query = "INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`) VALUES (:first_name, :last_name, :email, :password)";
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
}