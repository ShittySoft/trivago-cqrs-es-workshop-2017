<?php

declare(strict_types=1);

namespace Building\Factory\EventHandler;

use Building\Infrastructure\EventHandler\PersonCheckedInEventLog;
use Interop\Container\ContainerInterface;

final class PersonCheckedInEventHandlerFactory
{
    public function __invoke(ContainerInterface $container) : array
    {
        return [
            new PersonCheckedInEventLog(),
        ];
    }
}
