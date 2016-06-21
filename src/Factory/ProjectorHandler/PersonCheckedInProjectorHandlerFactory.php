<?php

declare(strict_types=1);

namespace Building\Factory\ProjectorHandler;

use Building\Infrastructure\Projector\WriteCheckedInUsersToCurrentCheckedInUsers;
use Interop\Container\ContainerInterface;

final class PersonCheckedInProjectorHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return [
            new WriteCheckedInUsersToCurrentCheckedInUsers(),
        ];
    }
}
