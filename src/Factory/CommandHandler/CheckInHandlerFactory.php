<?php

declare(strict_types=1);

namespace Building\Factory\CommandHandler;

use Building\Infrastructure\CommandHandler\CheckInHandler;
use Building\Infrastructure\Repository\BuildingRepository;
use Interop\Container\ContainerInterface;

final class CheckInHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CheckInHandler
    {
        return new CheckInHandler($container->get(BuildingRepository::class));
    }
}
