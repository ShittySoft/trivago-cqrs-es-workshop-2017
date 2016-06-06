<?php

declare(strict_types=1);

namespace Building\Factory\CommandHandler;

use Building\Infrastructure\CommandHandler\CheckOutHandler;
use Building\Infrastructure\Repository\BuildingRepository;
use Interop\Container\ContainerInterface;

final class CheckOutHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CheckOutHandler
    {
        return new CheckOutHandler($container->get(BuildingRepository::class));
    }
}
