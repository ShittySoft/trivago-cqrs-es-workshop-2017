<?php

declare(strict_types=1);

namespace Building\Factory\Services;

use Doctrine\DBAL\Connection;
use Interop\Container\ContainerInterface;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\ActionEventListenerAggregate;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\Common\Messaging\NoOpMessageConverter;
use Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter;
use Prooph\EventStore\Adapter\PayloadSerializer\JsonPayloadSerializer;
use Prooph\EventStore\EventStore;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;

final class ProjectorService
{
    public function __invoke(ContainerInterface $container)
    {
        $eventBus   = new EventBus();
        $eventStore = new EventStore(
            new DoctrineEventStoreAdapter(
                $container->get(Connection::class),
                new FQCNMessageFactory(),
                new NoOpMessageConverter(),
                new JsonPayloadSerializer()
            ),
            new ProophActionEventEmitter()
        );

        $eventBus->utilize($this->buildListener());

        (new EventPublisher($eventBus))->setUp($eventStore);
    }

    public function buildListener()
    {
        return new class ($container) implements ActionEventListenerAggregate
        {
            /**
             * @var ContainerInterface
             */
            private $projectors;

            public function __construct(ContainerInterface $projectors)
            {
                $this->projectors = $projectors;
            }

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
                $messageName = (string) $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_NAME);

                if ($this->projectors->has($messageName . '-projector')) {
                    $actionEvent->setParam(
                        EventBus::EVENT_PARAM_EVENT_LISTENERS,
                        $this->projectors->get($messageName . '-projector')
                    );
                }
            }
        };
    }
}
