<?php

declare(strict_types=1);

namespace Building\Factory\CommandHandler;

use Building\Domain\Repository\BuildingRepositoryInterface;
use Building\Infrastructure\CommandHandler\RegisterNewBuildingHandler;
use Interop\Container\ContainerInterface;

final class RegisterNewBuildingHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RegisterNewBuildingHandler
    {
        return new RegisterNewBuildingHandler($container->get(BuildingRepositoryInterface::class));
    }
}
