<?php

namespace App\GraphQL\Resolver;

use App\Entity\Poll;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class PollResolver implements QueryInterface, AliasedInterface
{
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
        
    }

    public function resolve(int $id)
    {
        $repository = $this->entityManagerInterface->getRepository(Poll::class);
        return $repository->find($id);
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'Poll',
        ];
    }
}