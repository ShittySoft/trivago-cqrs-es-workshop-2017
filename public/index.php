<?php

declare(strict_types=1);

use Building\Domain\Aggregate\Building;
use Building\Domain\Command;
use Building\Domain\DomainEvent;
use Building\Domain\Repository\BuildingRepositoryInterface;
use Building\Factory\CommandHandler as CommandHandlerFactory;
use Building\Factory\EventHandler as EventHandlerFactory;
use Building\Factory\Services\ProjectorService;
use Building\Infrastructure\CommandHandler;
use Building\Infrastructure\Repository\BuildingRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\SchemaException;
use Interop\Container\ContainerInterface;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\ActionEventListenerAggregate;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\Common\Messaging\NoOpMessageConverter;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter;
use Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema;
use Prooph\EventStore\Adapter\PayloadSerializer\JsonPayloadSerializer;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\EventStore;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rhumsaa\Uuid\Uuid;

call_user_func(function () {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once __DIR__ . '/../vendor/autoload.php';

    $sm = new \Zend\ServiceManager\ServiceManager([
        'factories' => [
            Connection::class => function () {
                $connection = DriverManager::getConnection([
                    'driverClass' => Driver::class,
                    'path'        => __DIR__ . '/../data/db.sqlite3',
                ]);

                try {
                    $schema = $connection->getSchemaManager()->createSchema();

                    EventStoreSchema::createSingleStream($schema, 'event_stream', true);

                    foreach ($schema->toSql($connection->getDatabasePlatform()) as $sql) {
                        $connection->exec($sql);
                    }
                } catch (SchemaException $ignored) {
                }

                return $connection;
            },

            EventStore::class                  => function (ContainerInterface $container) {
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

                $eventBus->utilize(new class ($container) implements ActionEventListenerAggregate
                {
                    /**
                     * @var ContainerInterface
                     */
                    private $eventHandlers;

                    public function __construct(ContainerInterface $projectors)
                    {
                        $this->eventHandlers = $projectors;
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

                        if ($this->eventHandlers->has($messageName)) {
                            $actionEvent->setParam(
                                EventBus::EVENT_PARAM_EVENT_LISTENERS,
                                $this->eventHandlers->get($messageName)
                            );
                        }
                    }
                });

                (new EventPublisher($eventBus))->setUp($eventStore);

                return $eventStore;
            },

            // Projector
            ProjectorService::class            => ProjectorService::class,

            // Services
            CommandBus::class                  => \Building\Factory\Services\CommandBus::class,

            // Command -> CommandHandlerFactory
            Command\CheckIn::class             => CommandHandlerFactory\CheckInHandlerFactory::class,
            Command\CheckOut::class            => CommandHandlerFactory\CheckOutHandlerFactory::class,
            Command\RegisterNewBuilding::class => CommandHandlerFactory\RegisterNewBuildingHandlerFactory::class,

            DomainEvent\PersonCheckedIn::class => EventHandlerFactory\PersonCheckedInEventHandlerFactory::class,
            DomainEvent\PersonCheckedOut::class => EventHandlerFactory\PersonCheckedOutEventHandlerFactory::class,

            DomainEvent\PersonCheckedIn::class . '-projector' => \Building\Factory\ProjectorHandler\PersonCheckedInProjectorHandlerFactory::class,

            BuildingRepositoryInterface::class => function (ContainerInterface $container) : BuildingRepositoryInterface {
                return new BuildingRepository(
                    new AggregateRepository(
                        $container->get(EventStore::class),
                        AggregateType::fromAggregateRootClass(Building::class),
                        new AggregateTranslator()
                    )
                );
            },
        ],
    ]);

    $app = Zend\Expressive\AppFactory::create($sm);

    $app->get('/', function (Request $request, Response $response, callable $out = null) {
        ob_start();
        require __DIR__ . '/../template/index.php';
        $content = ob_get_clean();

        return $response->getBody()->write($content);
    });

    $app->post('/register-new-building', function (Request $request, Response $response, callable $out = null) use ($sm) {
        $commandBus = $sm->get(CommandBus::class);
        $commandBus->dispatch(Command\RegisterNewBuilding::fromName($request->getParsedBody()['name']));

        return $response->withAddedHeader('Location', '/');
    });

    $app->get('/building/{buildingId}', function (Request $request, Response $response, callable $out = null) {
        $buildingId = Uuid::fromString($request->getAttribute('buildingId'));

        ob_start();
        require __DIR__ . '/../template/building.php';
        $content = ob_get_clean();

        return $response->getBody()->write($content);
    });

    $app->post('/checkin/{buildingId}', function (Request $request, Response $response, callable $out = null) use ($sm) {
        $buildingId = Uuid::fromString($request->getAttribute('buildingId'));
        $commandBus = $sm->get(CommandBus::class);

        $commandBus->dispatch(Command\CheckIn::fromBuildingIdAndUserName(
            $buildingId,
            $request->getParsedBody()['username'])
        );

        return $response->withAddedHeader('Location', '/building/' . $buildingId);
    });

    $app->post('/checkout/{buildingId}', function (Request $request, Response $response, callable $out = null) use ($sm) {
        $buildingId = Uuid::fromString($request->getAttribute('buildingId'));
        $commandBus = $sm->get(CommandBus::class);

        $commandBus->dispatch(Command\CheckOut::fromBuildingIdAndUserName(
            $buildingId,
            $request->getParsedBody()['username'])
        );

        return $response->withAddedHeader('Location', '/building/' . $buildingId);
    });

    $app->run();
});