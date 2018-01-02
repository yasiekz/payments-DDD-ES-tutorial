<?php

namespace App\Domain\User;

class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Address
     */
    private $address;

    /**
     * @param string $id
     * @param string $name
     * @param Address $address
     */
    public function __construct(string $id, string $name, Address $address)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
    }

    /**
     * @param string $newName
     */
    public function changeName(string $newName): void
    {
        $this->name = $newName;
    }

    /**
     * @param Address $newAddress
     */
    public function changeAddress(Address $newAddress): void
    {
        $this->address = $newAddress;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->address->getStreet();
    }

    /**
     * @return string
     */
    public function getPost(): string
    {
        return $this->address->getPost();
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->address->getPostCode();
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->address->getCity();
    }
}