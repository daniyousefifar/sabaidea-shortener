<?php

namespace App\Repositories\Database;

use App\Exceptions\LinkNotFoundException;
use App\Repositories\LinkRepositoryInterface;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class LinkRepository implements LinkRepositoryInterface
{
    protected ContainerInterface $ci;

    public function __construct(ContainerInterface $container)
    {
        $this->ci = $container;
    }

    /**
     * Get All Links
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findAll(): array
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `links` ORDER BY `id` DESC");
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find Links by User ID
     *
     * @param int $user_id
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findAllByUserId(int $user_id): array
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `links` WHERE user_id=:user_id ORDER BY `id` DESC");
        $sth->execute([
            'user_id' => $user_id,
        ]);

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find Link by Link ID
     *
     * @param int $id
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws LinkNotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function findLinkById(int $id)
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `links` WHERE id=:id");
        $sth->execute(['id' => $id]);

        $data = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new LinkNotFoundException();
        }

        return $data;
    }

    /**
     * Find Link by URL
     *
     * @param string $url
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws LinkNotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function findLinkByUrl(string $url)
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `links` WHERE url=:url");
        $sth->execute(['url' => $url]);

        $data = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new LinkNotFoundException();
        }

        return $data;
    }

    /**
     * Find Link by Code
     *
     * @param string $code
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws LinkNotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function findLinkByCode(string $code)
    {
        $db = $this->ci->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `links` WHERE code=:code");
        $sth->execute(['code' => $code]);

        $data = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new LinkNotFoundException();
        }

        return $data;
    }

    /**
     * Store new link into database
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

            $query = "INSERT INTO `links` (`url`, `user_id`, `domain_id`) VALUES (:url, :user_id, :domain_id)";
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
     * Update special link
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

            $query = "UPDATE `links` SET url = :url, domain_id = :domain_id WHERE id = :id";
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
     * Delete special link
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

            $query = "DELETE FROM `links` WHERE `id` = :id";
            $statement = $db->prepare($query);
            $result = $statement->execute(['id' => $id]);

            $db->commit();

            return $result;
        } catch (\Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Store link code into database
     *
     * @param int $id
     * @param string $code
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function storeCode(int $id, string $code): string
    {
        $db = $this->ci->get(PDO::class);

        try {
            $db->beginTransaction();

            $query = "UPDATE `links` SET `code` = :code WHERE `id` = :id";
            $statement = $db->prepare($query);
            $result = $statement->execute([
                'code' => $code,
                'id' => $id,
            ]);

            $db->commit();

            return $result;
        } catch (\Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
    }
}