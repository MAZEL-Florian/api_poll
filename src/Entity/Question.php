<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity()]
#[ORM\Table(name: 'questions')]
class Question{

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $wording= null;

    #[Ignore]
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Collection $answers;

    #[ORM\ManyToOne(targetEntity: Poll::class, inversedBy: 'questions')]
    #[Ignore]
    private ?Poll $poll = null;

    
    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(int $id): Question
    {
        $this->id = $id;
        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }


    public function setWording(string $wording): Question
    {
        $this->wording = $wording;
        return $this;
    }

    public function getAnswers(): ?Collection
    {
        return $this->answers;
    }

    public function addAnswer(?Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }
        return $this;
    }


    public function removeAnswer(?Answer $answer): self
    {
        if($this->answers->removeElement($answer))
        {
            if($answer->getQuestion() === $this){
                $answer->setQuestion(null);
            }
        }
        return $this;

    }

    public function setAnswers(array $answers): self
    {
        $this->answers->clear();

        foreach($answers as $answer)
        {
            $answer = (new Answer())
            ->setWording($answer['wording']);

            $this->addAnswer($answer);
        }
        return $this;
    }
    public function getPoll(): ?Poll
    {
        return $this->poll;
    }

    public function setPoll(?Poll $poll): Question
    {
        $this->poll = $poll;
        return $this;
    }
}