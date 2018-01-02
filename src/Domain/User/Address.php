<?php

namespace App\Domain\User;

class Address
{
    /**
     * @var string
     */
    private $street;
    /**
     * @var string
     */
    private $post;
    /**
     * @var PostCode
     */
    private $postCode;
    /**
     * @var string
     */
    private $city;

    /**
     * @param string $street
     * @param string $post
     * @param PostCode $postCode
     * @param string $city
     */
    public function __construct($street, $post, PostCode $postCode, $city)
    {
        $this->street = $street;
        $this->post = $post;
        $this->postCode = $postCode;
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getPost(): string
    {
        return $this->post;
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode->getCode();
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }
}