<?php

declare(strict_types=1);

namespace Building\Factory\CommandHandler;

use Building\Domain\Repository\BuildingRepositoryInterface;
use Building\Infrastructure\CommandHandler\CheckInHandler;
use Interop\Container\ContainerInterface;

final class CheckInHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CheckInHandler
    {
        return new CheckInHandler($container->get(BuildingRepositoryInterface::class));
    }
}
