<?php

namespace App\GraphQL\Resolver;

use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class PollsResolver implements ResolverInterface, AliasedInterface
{
    public static function getAliases(): array
    {
        return [
            
        ];
    }
}