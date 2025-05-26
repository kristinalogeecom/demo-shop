<?php

namespace DemoShop\Application\BusinessLogic\Model;

class CategoryModel
{
    private ?int $id;
    private ?int $parentId;
    private string $name;
    private string $code;
    private string $description;

    /**
     * @param ?int $id
     * @param ?int $parentId
     * @param string $name
     * @param string $code
     * @param string $description
     */
    public function __construct(?int $id, ?int $parentId, string $name, string $code, string $description)
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
    }

    public function getId(): ?int
    {
        return $this->id !== null ? (int) $this->id : null;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


}