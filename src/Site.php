<?php

namespace Hexlet\Project;

class Site
{
    private ?int $id;
    private ?string $name;
    private ?string $createdAt;

    public static function fromArray(array $data): Site
    {
        [$name, $createdAt] = $data;
        $site = new self();
        $site->setTitle($name);
        $site->setCreatedAt($createdAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        
        return $this->name;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function exists(): bool
    {
        return !is_null($this->id);
    }

}