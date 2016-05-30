<?php


declare(strict_types=1);

use Hanoi\Infrastructure\Middleware;
use Hanoi\Domain\Command;
use Hanoi\Infrastructure\CommandHandler;
use Hanoi\Factory\CommandHandler as CommandHandlerFactory;
use Interop\Container\ContainerInterface;
use Prooph\ServiceBus\CommandBus;

require __DIR__ . '/../vendor/autoload.php';

$sm = new \Zend\ServiceManager\ServiceManager([
    'factories' => [

        // Services
        CommandBus::class => \Hanoi\Factory\Services\CommandBus::class,

        // Command -> CommandHandlerFactory
        Command\CreateNewGame::class => CommandHandlerFactory\CreateNewGameHandlerFactory::class,

        // Middlewares
        Middleware\Index::class => function () : Middleware\Index {
            return new Middleware\Index();
        },
        Middleware\Solve::class => function (ContainerInterface $sm) : Middleware\Solve {
            return new Middleware\Solve($sm->get(CommandBus::class));
        },
    ],
]);

$app = Zend\Expressive\AppFactory::create($sm);

$app->get(Middleware\Index::PATH, Middleware\Index::class);
$app->post(Middleware\Solve::PATH, Middleware\Solve::class);

$app->run();
