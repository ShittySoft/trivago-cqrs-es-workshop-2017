<?php

declare(strict_types=1);

namespace Hanoi\Factory\CommandHandler;

use Hanoi\Infrastructure\CommandHandler\CheckOutHandler;
use Hanoi\Infrastructure\Repository\BuildingRepository;
use Interop\Container\ContainerInterface;

final class CheckOutHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CheckOutHandler
    {
        return new CheckOutHandler($container->get(BuildingRepository::class));
    }
}
