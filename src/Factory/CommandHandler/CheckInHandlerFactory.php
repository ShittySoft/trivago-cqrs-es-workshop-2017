<?php

declare(strict_types=1);

namespace Hanoi\Factory\CommandHandler;

use Hanoi\Infrastructure\CommandHandler\CheckInHandler;
use Hanoi\Infrastructure\Repository\BuildingRepository;
use Interop\Container\ContainerInterface;

final class CheckInHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new CheckInHandler($container->get(BuildingRepository::class));
    }
}
