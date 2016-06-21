<?php

declare(strict_types=1);

namespace Building\Factory\ProjectorHandler;

use Building\Infrastructure\Projector\AddCheckedInUserToCurrentCheckedInUsers;
use Interop\Container\ContainerInterface;

final class PersonCheckedInProjectorsFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return [
            new AddCheckedInUserToCurrentCheckedInUsers(),
        ];
    }
}
