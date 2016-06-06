<?php

declare(strict_types=1);

error_reporting(-1);
ini_set('display_errors', '1');

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\SchemaException;
use Hanoi\Domain\Aggregate\Building;
use Hanoi\Domain\Command;
use Hanoi\Infrastructure\CommandHandler;
use Hanoi\Factory\CommandHandler as CommandHandlerFactory;
use Hanoi\Infrastructure\Repository\BuildingRepository;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\Common\Messaging\NoOpMessageConverter;
use Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema;
use Prooph\EventStore\Adapter\PayloadSerializer\JsonPayloadSerializer;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\EventStore;
use Prooph\ServiceBus\CommandBus;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

$sm = new \Zend\ServiceManager\ServiceManager([
    'factories' => [
        // Infrastructure
        AggregateRepository::class => function (\Interop\Container\ContainerInterface $container) {
            return new AggregateRepository(
                $container->get(EventStore::class),
                \Prooph\EventStore\Aggregate\AggregateType::fromAggregateRootClass(Building::class),
                new \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator()
            );
        },

        Connection::class => function (\Interop\Container\ContainerInterface $container) {
            $connection = \Doctrine\DBAL\DriverManager::getConnection(
                [
                    'driverClass' => \Doctrine\DBAL\Driver\PDOSqlite\Driver::class,
                    'path'        => __DIR__ . '/db.sqlite3',
                ]
            );

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

        EventStore::class => function (\Interop\Container\ContainerInterface $container) {
            return new EventStore(
                new \Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter(
                    $container->get(Connection::class),
                    new FQCNMessageFactory(),
                    new NoOpMessageConverter(),
                    new JsonPayloadSerializer()
                ),
                new \Prooph\Common\Event\ProophActionEventEmitter()
            );
        },

        // Services
        CommandBus::class => \Hanoi\Factory\Services\CommandBus::class,

        // Command -> CommandHandlerFactory
        Command\CheckIn::class => CommandHandlerFactory\CheckInHandlerFactory::class,
        Command\CheckOut::class => CommandHandlerFactory\CheckOutHandlerFactory::class,
        Command\RegisterNewBuilding::class => CommandHandlerFactory\RegisterNewBuildingHandlerFactory::class,

        BuildingRepository::class => function (\Interop\Container\ContainerInterface $container) {
            return new BuildingRepository($container->get(EventStore::class));
        }
    ],
]);

$app = Zend\Expressive\AppFactory::create($sm);

$app->get('/', function (Request $request, Response $response, callable $out = null) {
    return $response->getBody()->write(<<<'HTML'

<h1>Register Building</h1>

<form action="/register" method="post">
    <input type="text" name="name" placeholder="Building name">

    <button>Register</button>
</form>

HTML
    );
});

$app->post('/register', function (Request $request, Response $response, callable $out = null) use ($sm) {
    $commandBus = $sm->get(CommandBus::class);
    $commandBus->dispatch(Command\RegisterNewBuilding::fromName($request->getParsedBody()['name']));

    return $response->withAddedHeader('Location', '/');
});

$app->get('/build', function (Request $request, Response $response, callable $out = null) {
    return $response->getBody()->write(<<<'HTML'

<h1>Welcome to CQRS+ES building</h1>

<h2>Check In: </h2>
<form action="/checkin" method="post">
    <select name="username" placeholder="Enter with your username">
      <option selected disabled>-- Choice someone --</option>
      <option value="ocramius">Ocramius</option>
      <option value="malukenho">Malukenho</option>
    </select>

    <button>CheckIn</button>
</form>

<h2>Check Out: </h2>
<form action="/checkout" method="post">
    <select name="username" placeholder="Enter with your username">
      <option selected disabled>-- Choice someone --</option>
      <option value="ocramius">Ocramius</option>
      <option value="malukenho">Malukenho</option>
    </select>
    
    <button>CheckOut</button>
</form>

HTML
);
});

$app->post('/checkin', function (Request $request, Response $response, callable $out = null) use ($sm) {
    $commandBus = $sm->get(CommandBus::class);
    $commandBus->dispatch(Command\CheckIn::fromUserName($request->getParsedBody()['username']));

    return $response->withAddedHeader('Location', '/');
});

$app->post('/checkout', function (Request $request, Response $response, callable $out = null) use ($sm) {
    $commandBus = $sm->get(CommandBus::class);
    $commandBus->dispatch(Command\CheckOut::fromUserName($request->getParsedBody()['username']));

    return $response->withAddedHeader('Location', '/');
});


$app->run();
