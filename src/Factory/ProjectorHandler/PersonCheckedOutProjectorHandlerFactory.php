<?php

declare(strict_types=1);

namespace Building\Factory\ProjectorHandler;

use Building\Infrastructure\Projector\RemoveCheckedOutFromCurrentCheckedInUsers;
use Interop\Container\ContainerInterface;

final class PersonCheckedOutProjectorHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return [
            new RemoveCheckedOutFromCurrentCheckedInUsers(),
        ];
    }
}
