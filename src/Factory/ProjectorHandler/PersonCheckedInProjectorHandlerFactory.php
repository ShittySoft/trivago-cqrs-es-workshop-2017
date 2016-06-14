<?php

declare(strict_types=1);

namespace Building\Factory\ProjectorHandler;

use Infrastructure\Projector\WriteToConsole;
use Interop\Container\ContainerInterface;

final class PersonCheckedInProjectorHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return [
            new WriteToConsole(),
        ];
    }
}
