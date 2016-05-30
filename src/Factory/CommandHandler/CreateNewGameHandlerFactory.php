<?php

declare(strict_types=1);

namespace Hanoi\Factory\CommandHandler;

use Hanoi\Infrastructure\CommandHandler\CreateNewGameHandler;
use Interop\Container\ContainerInterface;

final class CreateNewGameHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CreateNewGameHandler
    {
        return new CreateNewGameHandler();
    }
}
