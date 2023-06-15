<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'participations')]
class Participation{

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private  ?\DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: Poll::class)]
    private ?Poll $poll = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'participations')]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Choice::class, mappedBy: 'participations', cascade: ['persist', 'remove'], orphanRemoval: true)]
private ?Collection $choices;

    

    public function __construct()
    {
        $this->date = New \DateTime();
        $this->choices = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate($date): Participation
    {
        $this->date = $date;
        return $this;
    }

    public function getPoll(): ?Poll
    {
        return $this->poll;
    }

    public function setPoll(?Poll $poll): Participation
    {
        $this->poll = $poll;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Participation
    {
        $this->user = $user;
        return $this;
    }

    public function getChoices(): ?Collection
    {
        return $this->choices;
    }

    public function addChoice(?Choice $choice): self
    {
        if (!$this->choices->contains($choice)) {
            $this->choices[] = $choice;
            $choice->setParticipation($this);
        }
        return $this;
    }


    public function removeChoice(?Choice $choice): self
    {
        if($this->choices->removeElement($choice))
        {
            if($choice->getParticipation() === $this){
                $choice->setParticipation(null);
            }
        }
        return $this;

    }
}