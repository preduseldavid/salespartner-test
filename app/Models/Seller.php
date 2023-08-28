<?php

namespace App\Models;

class Seller extends BaseModel
{
    const TABLE_NAME = 'sellers';
    const PRIMARY_KEY_NAME = 'id';

    protected int $id;
    protected string $name;
    protected ?string $created_at;
    protected ?string $updated_at;

    public function __construct()
    {
        $this->setDbName(getenv('DB_DATABASE'));
        $this->setTableName(self::TABLE_NAME);
        $this->setPrimaryKeyName(self::PRIMARY_KEY_NAME);

        parent::__construct();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}