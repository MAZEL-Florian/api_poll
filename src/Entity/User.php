<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'users')]
class User{

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $firstname= null;

    #[ORM\Column(type: 'string')]
    private  ?string $lastname = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $role = null;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    private ?Address $address = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }


    public function setFirstname(string $firstname): User
    {
        $this->firstname = $firstname;
        return $this;
    }


    public function getLastname(): ?string
    {
        return $this->lastname;
    }


    public function setLastname(string $lastname): User
    {
        $this->lastname = $lastname;
        return $this;
    }


    public function getRole(): ?string
    {
        return $this->role;
    }


    public function setRole(string $role): User
    {
        $this->role = $role;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address|array $address): User
    {
        if($address instanceof Address) {
            $this->address = $address;
        }
        else if (is_array($address)) {
            $this->getAddress()->setStreet($address['street'])
            ->setCity($address['city'])
            ->setCountry($address['country']);
        }
        return $this;
    }
}