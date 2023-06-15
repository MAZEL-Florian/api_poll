<?php


namespace App\GraphQL\Mutation;

use App\Entity\Poll;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class PollMutation implements MutationInterface, AliasedInterface
{

    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
        
    }

    public function resolve($args)
    {
        $poll = (new Poll())
        ->setTitle($args[0]['title']);

        $this->entityManagerInterface->persist($poll);
        $this->entityManagerInterface->flush();

        return ['content' => $poll];
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'createPoll'
        ];
    }
}