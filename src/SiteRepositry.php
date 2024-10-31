<?php

namespace Hexlet\Project;

class SiteRepositry
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * [Description for getEntities]
     *
     * @return array<Site>
     *
     */
    public function getEntities(): array
    {
        $sites = [];
        $stmt = $this->conn->query('SELECT * FROM urls_689c');
        if ($stmt) {
            while ($row = $stmt->fetch()) {
                /**
                 * @var array{name:string, created_at:string, id:int} $row
                 */
                $site = Site::fromArray([$row['name'], $row['created_at']]);
                $id = $row['id'];
                $site->setId($id);
                $checker = new Checker($this->conn);
                $checks = $checker->getChecks($id, true);
                $site->setChecks($checks);
                $sites[] = $site;
            }
        }
        return $sites;
    }

    /**
     * [Description for find]
     *
     * @param int $id
     *
     * @return Site|null
     *
     */
    public function find(int $id): ?Site
    {
        $stmt = $this->conn->prepare('SELECT * FROM urls_689c WHERE id = :id');
        $stmt->execute([$id]);
        if ($stmt) {
            if ($row = $stmt->fetch()) {
                /**
                 * @var array{name:string, created_at:string, id:int} $row
                 */
                $site = Site::fromArray([$row['name'], $row['created_at']]);
                $site->setId($row['id']);
                return $site;
            }
        }
        return null;
    }

    /**
     * [Description for findByName]
     *
     * @param string $name
     *
     * @return int<min, -1>|int<1, max>|null
     *
     */
    public function findByName(string $name): ?int
    {
        $stmt = $this->conn->prepare('SELECT id FROM urls_689c WHERE name = :name');
        $stmt->execute([$name]);
        if ($id = $stmt->fetchColumn()) {
            return $id;
        }
        return null;
    }

    public function save(Site $site): void
    {
        if ($site->exists()) {
            $this->update($site);
        } else {
            $this->create($site);
        }
    }

    public function create(Site $site): void
    {
        $stmt = $this->conn->prepare('INSERT INTO urls_689c (name, created_at) VALUES (:name, :created_at)');
        $stmt->execute(
            [
                $site->getName(),
                $site->getCreatedAt()
            ]
        );
        $id = (int)$this->conn->lastInsertId();
        $site->setId($id);
    }

    public function update(Site $site): void
    {
        $stmt = $this->conn->prepare('UPDATE urls_689c SET name = :name, created_at = :created_at WHERE id = :id');
        $stmt->execute(
            [
                $site->getName(),
                $site->getCreatedAt(),
                $site->getId()
            ]
        );
    }
}
