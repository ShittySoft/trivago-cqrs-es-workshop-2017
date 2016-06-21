<?php

declare(strict_types=1);

namespace Building\Factory\EventHandler;

use Building\Infrastructure\EventHandler\PersonCheckedOutEventLog;
use Interop\Container\ContainerInterface;

final class PersonCheckedOutEventHandlersFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return [
            new PersonCheckedOutEventLog(),
        ];
    }
}
