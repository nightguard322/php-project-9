<?php

namespace Hexlet\Project;

class Site
{
    private ?int $id = null;
    private ?string $name;
    private ?string $createdAt;
    private ?array $checks;

    public static function fromArray(array $data): Site
    {
        [$name, $createdAt] = $data;
        $site = new self();
        $site->setName($name);
        $site->setCreatedAt($createdAt);
        return $site;
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

    public function getChecks()
    {
        return $this->checks;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setChecks($checks): void
    {
        $this->checks = $checks;
    }

    public function exists(): bool
    {
        return !is_null($this->id);
    }
}
