<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity()]
#[ORM\Table(name: 'choices')]
class Choice{

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[Ignore]
    #[ORM\OneToOne(targetEntity: Question::class)]
    private ?Question $question = null;

    #[ORM\OneToOne(targetEntity: Answer::class, inversedBy: 'choices')]
    private ?Answer $answer = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Participation::class, inversedBy: 'choices')]
    private ?Participation $participation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Choice
    {
        $this->id = $id;
        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): Choice
    {
        $this->question = $question;
        return $this;
    }

    public function getAnswer(): ?Answer
    {
        return $this->answer;
    }

    public function setAnswer(?Answer $answer): Choice
    {
        $this->answer = $answer;
        return $this;
    }

    public function getParticipation(): ?Participation
    {
        return $this->participation;
    }

    public function setParticipation(?Participation $participation): Choice
    {
        $this->participation = $participation;
        return $this;
    }
}