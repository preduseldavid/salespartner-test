<?php

namespace App\Models;

class Lead extends BaseModel
{
    const TABLE_NAME = 'leads';
    const PRIMARY_KEY_NAME = 'id';

    protected int $id;
    protected int $seller_id;
    protected string $sellerStartTimestamp;
    protected string $email;
    protected string $first_name;
    protected string $last_name;
    protected string $phone;
    protected string $message;
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

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSellerId(): int
    {
        return $this->seller_id;
    }

    public function setSellerId(int $seller_id): self
    {
        $this->addDataChanges('seller_id', $seller_id);
        $this->seller_id = $seller_id;

        return $this;
    }

    public function getSellerStartTimestamp(): string
    {
        return $this->sellerStartTimestamp;
    }

    public function setSellerStartTimestamp(string $sellerStartTimestamp): self
    {
        $this->addDataChanges('seller_start_timestamp', $sellerStartTimestamp);
        $this->sellerStartTimestamp = $sellerStartTimestamp;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->addDataChanges('email', $email);
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->addDataChanges('first_name', $first_name);
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->addDataChanges('last_name', $last_name);
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->addDataChanges('phone', $phone);
        $this->phone = $phone;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->addDataChanges('message', $message);
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
