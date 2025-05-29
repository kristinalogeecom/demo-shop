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

    /**
     * @param array $data
     * @return self
     */
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

    /**
     * @return array
     */
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

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return void
     */
    public function setCode(?string $code): void
    {
        $this->code = $code !== '' ? $code : null;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description !== '' ? $description : null;
    }
}
