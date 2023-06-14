<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'polls')]
class Poll{

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $title = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'poll')]
    private ?Collection $questions;


    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->questions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(int $id): Poll
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }


    public function setTitle(string $title): Poll
    {
        $this->title = $title;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedat(\DateTimeInterface $createdAt): Poll
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getQuestions(): ?Collection
    {
        return $this->questions;
    }

    public function addQuestion(?Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setPoll($this);
        }
        return $this;
    }


    public function removeQuestion(?Question $question): self
    {
        if($this->questions->removeElement($question))
        {
            if($question->getPoll() === $this){
                $question->setPoll(null);
            }
        }
        return $this;

    }

    public function setQuestions(array $questions): self
    {
        $this->questions->clear();

        foreach($questions as $question)
        {
            $questions = (new Question())
            ->setWording($question['wording']);

            $this->addQuestion($question);
        }
        return $this;
    }
}