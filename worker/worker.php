<?php

declare(strict_types=1);

namespace Building\Worker;

use Bernard\Consumer;
use Bernard\Queue;
use Interop\Container\ContainerInterface;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Message\Bernard\BernardRouter;
use Symfony\Component\EventDispatcher\EventDispatcher;

call_user_func(function () {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once __DIR__ . '/../vendor/autoload.php';

    /* @var $sm ContainerInterface */
    $sm = require __DIR__ . '/../container.php';

    (new Consumer(new BernardRouter($sm->get(CommandBus::class), new EventBus()), new EventDispatcher()))
        ->consume($sm->get(Queue::class));
});
