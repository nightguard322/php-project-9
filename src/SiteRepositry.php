<?php

namespace Hexlet\Project;

class SiteRepositry
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getEntities(): array
    {
        $sites = [];
        $stmt = $this->conn->query('SELECT * FROM urls');
        while($row = $stmt->fetch()) {
            $site = Site::fromArray([$row['name'], $row['created_at']]);
            $site->setId($row['id']);
            $sites[] = $site;
        }
        return $sites;
    }

    public function find($id): ?Site
    {
        $stmt = $this->conn->prepare('SELECT * FROM urls WHERE id = :id');
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            $site = Site::fromArray([$row['name'], $row['created_at']]);
            $site->setId($row['id']);
            return $site;
        }
        return null;
    }

    public function findByName($name): ?int
    {
        $stmt = $this->conn->prepare('SELECT id FROM urls WHERE name = :name');
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

    public function create(Site $site)
    {
        $stmt = $this->conn->prepare('INSERT INTO urls (name, created_at) VALUES (:name, :created_at)');
        $stmt->execute(
            [
                $site->getName(),
                $site->getCreatedAt()
            ]
        );
        $id = $this->conn->lastInsertId();
        $site->setId($id);
    }

    public function update(Site $site)
    {
        $stmt = $this->conn->prepare('UPDATE urls SET name = :name, created_at = :created_at WHERE id = :id');
        $stmt->execute(
            [
                $site->getName(),
                $site->getCreatedAt(),
                $site->getId()
            ]
        );
    }
}