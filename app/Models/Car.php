<?php

namespace App\Models;

class Car extends BaseModel
{
    const PER_PAGE = 10;

    const TABLE_NAME = 'cars';
    const PRIMARY_KEY_NAME = 'id';

    protected int $id;
    protected string $brand;
    protected string $model;
    protected string $submodel;
    protected string $transmission;
    protected string $traction;
    protected string $fuel;
    protected int $km;
    protected string $stock_type;
    protected int $price_without_vat;
    protected string $interior_color;
    protected string $exterior_color;
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

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function getSubmodel(): string
    {
        return $this->submodel;
    }

    public function setSubmodel(string $submodel): void
    {
        $this->submodel = $submodel;
    }

    public function getTransmission(): string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): void
    {
        $this->transmission = $transmission;
    }

    public function getTraction(): string
    {
        return $this->traction;
    }

    public function setTraction(string $traction): void
    {
        $this->traction = $traction;
    }

    public function getFuel(): string
    {
        return $this->fuel;
    }

    public function setFuel(string $fuel): void
    {
        $this->fuel = $fuel;
    }

    public function getKm(): int
    {
        return $this->km;
    }

    public function setKm(int $km): void
    {
        $this->km = $km;
    }

    public function getStockType(): string
    {
        return $this->stock_type;
    }

    public function setStockType(string $stock_type): void
    {
        $this->stock_type = $stock_type;
    }

    public function getPriceWithoutVat(): int
    {
        return $this->price_without_vat;
    }

    public function setPriceWithoutVat(int $price_without_vat): void
    {
        $this->price_without_vat = $price_without_vat;
    }

    public function getInteriorColor(): string
    {
        return $this->interior_color;
    }

    public function setInteriorColor(string $interior_color): void
    {
        $this->interior_color = $interior_color;
    }

    public function getExteriorColor(): string
    {
        return $this->exterior_color;
    }

    public function setExteriorColor(string $exterior_color): void
    {
        $this->exterior_color = $exterior_color;
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
