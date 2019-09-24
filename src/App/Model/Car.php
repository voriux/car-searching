<?php

namespace App\Model;

use ReflectionClass;

class Car
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $href;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var int
     */
    protected $vat;

    /**
     * @var int
     */
    protected $finalPrice;

    /**
     * @var string
     */
    protected $productionYear;

    /**
     * @var int
     */
    protected $power;

    /**
     * @var string
     */
    protected $gearbox;

    /**
     * @var string
     */
    protected $bodyType;

    /**
     * @var string
     */
    protected $fuel;

    /**
     * @var int
     */
    protected $km;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Car
     */
    public function setTitle(string $title): Car
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Car
     */
    public function setDescription(string $description): Car
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Car
     */
    public function setImage(string $image): Car
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @param string $href
     * @return Car
     */
    public function setHref(string $href): Car
    {
        $this->href = $href;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return Car
     */
    public function setPrice(int $price): Car
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getVat(): int
    {
        return $this->vat;
    }

    /**
     * @param int $vat
     * @return Car
     */
    public function setVat(int $vat): Car
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductionYear(): string
    {
        return $this->productionYear;
    }

    /**
     * @param string $productionYear
     * @return Car
     */
    public function setProductionYear(string $productionYear): Car
    {
        $this->productionYear = $productionYear;

        return $this;
    }

    /**
     * @return int
     */
    public function getPower(): int
    {
        return $this->power;
    }

    /**
     * @param int $power
     * @return Car
     */
    public function setPower(int $power): Car
    {
        $this->power = $power;

        return $this;
    }

    /**
     * @return string
     */
    public function getGearbox(): string
    {
        return $this->gearbox;
    }

    /**
     * @param string $gearbox
     * @return Car
     */
    public function setGearbox(string $gearbox): Car
    {
        $this->gearbox = $gearbox;

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyType(): string
    {
        return $this->bodyType;
    }

    /**
     * @param string $bodyType
     * @return Car
     */
    public function setBodyType(string $bodyType): Car
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * @return string
     */
    public function getFuel(): string
    {
        return $this->fuel;
    }

    /**
     * @param string $fuel
     * @return Car
     */
    public function setFuel(string $fuel): Car
    {
        $this->fuel = $fuel;

        return $this;
    }

    /**
     * @return int
     */
    public function getKm(): int
    {
        return $this->km;
    }

    /**
     * @param int $km
     * @return Car
     */
    public function setKm(int $km): Car
    {
        $this->km = $km;

        return $this;
    }

    /**
     * @return int
     */
    public function getFinalPrice(): int
    {
        return $this->finalPrice;
    }

    /**
     * @param int $finalPrice
     */
    public function setFinalPrice(int $finalPrice): void
    {
        $this->finalPrice = $finalPrice;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function toArray(): array
    {
        $oReflectionClass = new ReflectionClass(self::class);
        $properties = [];
        foreach ($oReflectionClass->getProperties() as $property) {
            $properties[$property->getName()] = $this->{$property->getName()};
        }
        return $properties;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getAttributes(): array
    {
        $oReflectionClass = new ReflectionClass(self::class);
        $properties = [];
        foreach ($oReflectionClass->getProperties() as $property) {
            $properties[$property->getName()] = $property->getName();
        }
        return $properties;
    }
}