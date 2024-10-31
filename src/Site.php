<?php

namespace Hexlet\Project;

class Site
{
    private ?int $id = null;
    /**
     * @var string|null
     */
    private ?string $name;
    /**
     * @var string|null
     */
    private ?string $createdAt;
    /**
     * @var array<mixed>|null $checks
     */
    private ?array $checks;

    /**
     * [Description for fromArray]
     *
     * @param array<string> $data
     * 
     * @return Site
     * 
     */
    public static function fromArray(array $data): Site
    {
        /** @var array{0:string, 1:string}  $data*/
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

    /**
     * @return array<mixed>|null
     */
    public function getChecks(): array|null
    {
        return $this->checks;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param array<mixed> $checks
     * @return void
     */
    public function setChecks(array $checks): void
    {
        $this->checks = $checks;
    }

    public function exists(): bool
    {
        return !is_null($this->id);
    }
}
