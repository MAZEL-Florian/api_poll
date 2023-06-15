<?php

namespace App\Repository;

use App\Entity\Poll;
use Doctrine\ORM\EntityRepository;

class PollRepository extends EntityRepository
{
    public function getNumberOfPolls()
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Poll::class,'p')
        ;

        return $qb->getQuery()->getResult();
    }

    public function getPollByTitle(string $title) {
        return $this->_em->createQueryBuilder()
            ->select('p')
            ->from(Poll::class, 'p')
            ->where('p.title LIKE :title')
            ->setParameters([
                'title' => $title.'%'
            ])
            ->getQuery()
            ->getResult();
    }

    public function getPollWithQuestions(int $number) {
        return $this->_em->createQueryBuilder()
            ->select('p')
            ->from(Poll::class, 'p')
            ->join('p.questions', 'q')
            ->where('q.wording LIKE :title')
            ->setParameters([
                'title' => '%API%'
            ])
            ->getQuery()
            ->getResult();
    }
}