<?php

declare(strict_types=1);

namespace Hanoi\Factory\CommandHandler;

use Hanoi\Infrastructure\CommandHandler\RegisterNewBuildingHandler;
use Hanoi\Infrastructure\Repository\BuildingRepository;
use Interop\Container\ContainerInterface;

final class RegisterNewBuildingHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RegisterNewBuildingHandler
    {
        return new RegisterNewBuildingHandler($container->get(BuildingRepository::class));
    }
}
