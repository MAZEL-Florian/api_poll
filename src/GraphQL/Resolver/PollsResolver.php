<?php

namespace App\GraphQL\Resolver;

use App\Entity\Poll;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class PollsResolver implements QueryInterface, AliasedInterface
{
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
        
    }

    public function resolve() 
    {
        $repository = $this->entityManagerInterface->getRepository(Poll::class);
        return $repository->findAll();
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'Polls',
        ];
    }
}