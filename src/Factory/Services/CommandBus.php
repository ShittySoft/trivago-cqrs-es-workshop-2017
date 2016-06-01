<?php

declare(strict_types=1);

namespace Hanoi\Factory\Services;

use Interop\Container\ContainerInterface;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\ActionEventListenerAggregate;
use Prooph\EventStore\EventStore;
use Prooph\EventStoreBusBridge\TransactionManager;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\ServiceLocatorPlugin;

final class CommandBus
{
    public function __invoke(ContainerInterface $container) : \Prooph\ServiceBus\CommandBus
    {
        $commandBus = new \Prooph\ServiceBus\CommandBus();

        $commandBus->utilize(new ServiceLocatorPlugin($container));
        $commandBus->utilize($this->buildCommandRouter());

        $transactionManager = new TransactionManager();
        $transactionManager->setUp($container->get(EventStore::class));

        $commandBus->utilize($transactionManager);

        return $commandBus;
    }

    private function buildCommandRouter() : ActionEventListenerAggregate
    {
        return new class implements ActionEventListenerAggregate {
            public function attach(ActionEventEmitter $dispatcher)
            {
                $dispatcher->attachListener(MessageBus::EVENT_ROUTE, [$this, 'onRoute']);
            }

            public function detach(ActionEventEmitter $dispatcher)
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function onRoute(ActionEvent $actionEvent)
            {
                $actionEvent->setParam(
                    MessageBus::EVENT_PARAM_MESSAGE_HANDLER,
                    (string) $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_NAME)
                );
            }
        };
    }
}
