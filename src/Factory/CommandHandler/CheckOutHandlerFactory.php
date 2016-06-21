<?php

declare(strict_types=1);

namespace Building\Factory\CommandHandler;

use Building\Domain\Repository\BuildingRepositoryInterface;
use Building\Infrastructure\CommandHandler\CheckOutHandler;
use Interop\Container\ContainerInterface;

final class CheckOutHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CheckOutHandler
    {
        return new CheckOutHandler($container->get(BuildingRepositoryInterface::class));
    }
}
