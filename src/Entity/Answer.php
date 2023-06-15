<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'answers')]
class Answer{

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $wording= null;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'answers')]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Answer
    {
        $this->id = $id;
        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }


    public function setWording(string $wording): Answer
    {
        $this->wording = $wording;
        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): Answer
    {
        $this->question = $question;
        return $this;
    }

}