<?php

namespace DemoShop\Application\BusinessLogic\Model;

/**
 * Represents a category in the system.
 */
class CategoryModel
{
    private ?int $id;
    private ?int $parentId;
    private string $name;
    private ?string $code;
    private ?string $description;

    /**
     * @param int|null $id
     * @param int|null $parentId
     * @param string $name
     * @param string|null $code
     * @param string|null $description
     */
    public function __construct(
        ?int $id,
        ?int $parentId,
        string $name,
        ?string $code = null,
        ?string $description = null
    ) {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->name = trim($name);
        $this->code = $code !== '' ? $code : null;
        $this->description = $description !== '' ? $description : null;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null,
            isset($data['parent_id']) && $data['parent_id'] !== '' ? (int)$data['parent_id'] : null,
            $data['name'] ?? '',
            $data['code'] ?? null,
            $data['description'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
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
        $this->name = trim($name);
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code !== '' ? $code : null;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description !== '' ? $description : null;
    }
}
