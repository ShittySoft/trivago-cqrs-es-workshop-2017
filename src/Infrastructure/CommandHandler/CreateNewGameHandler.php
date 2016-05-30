<?php

namespace Hanoi\Infrastructure\CommandHandler;

use Hanoi\Domain\Command\CreateNewGame;

final class CreateNewGameHandler
{
    public function __invoke(CreateNewGame $command)
    {
        echo 'A new game was created ' . $command->uuid();
        exit;
    }
}
