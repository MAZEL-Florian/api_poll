<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'addresses')]
class Address{

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $street= null;

    #[ORM\Column(type: 'string')]
    private  ?string $city = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $country = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(int $id): Address
    {
        $this->id = $id;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }


    public function setStreet(string $street): Address
    {
        $this->street = $street;
        return $this;
    }


    public function getCity(): ?string
    {
        return $this->city;
    }


    public function setCity(string $city): Address
    {
        $this->city = $city;
        return $this;
    }


    public function getCountry(): ?string
    {
        return $this->country;
    }


    public function setCountry(string $country): Address
    {
        $this->country = $country;
        return $this;
    }
}