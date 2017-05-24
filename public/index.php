<?php

declare(strict_types=1);

namespace Building\App;

use Building\Domain\Command;
use Prooph\ServiceBus\CommandBus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rhumsaa\Uuid\Uuid;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Zend\Expressive\Application;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\WhoopsErrorHandler;

call_user_func(function () {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $sm = require __DIR__ . '/../container.php';

    //////////////////////////
    // Routing/frontend/etc //
    //////////////////////////

    // Error handling so that our eyes don't bleed: don't do this in production!
    $whoopsHandler = new PrettyPageHandler();
    $whoops        = new Run();

    $whoops->writeToOutput(false);
    $whoops->allowQuit(false);
    $whoops->pushHandler($whoopsHandler);

    $app = new Application(new FastRouteRouter(), $sm, new WhoopsErrorHandler($whoops, $whoopsHandler));

    $app->pipeRoutingMiddleware();

    $app->get('/', function (Request $request, Response $response) : Response {
        ob_start();
        require __DIR__ . '/../template/index.php';
        $content = ob_get_clean();

        $response->getBody()->write($content);

        return $response;
    });

    $app->post('/register-new-building', function (Request $request, Response $response) use ($sm) : Response {
        $commandBus = $sm->get(CommandBus::class);
        $commandBus->dispatch(Command\RegisterNewBuilding::fromName($request->getParsedBody()['name']));

        return $response->withAddedHeader('Location', '/');
    });

    $app->get('/building/{buildingId}', function (Request $request, Response $response) : Response {
        $buildingId = Uuid::fromString($request->getAttribute('buildingId'));

        ob_start();
        require __DIR__ . '/../template/building.php';
        $content = ob_get_clean();

        $response->getBody()->write($content);

        return $response;
    });

    $app->post('/checkin/{buildingId}', function (Request $request, Response $response) use ($sm) : Response {
        $buildingId = Uuid::fromString($request->getAttribute('buildingId'));

        $sm->get(CommandBus::class)->dispatch(Command\CheckInUser::fromBuildingIdAndUsername(
            $buildingId,
            $request->getParsedBody()['username']
        ));

        return $response->withAddedHeader('Location', '/building/' . $buildingId->toString());
    });

    $app->post('/checkout/{buildingId}', function (Request $request, Response $response) use ($sm) : Response {
        $buildingId = Uuid::fromString($request->getAttribute('buildingId'));

        $sm->get(CommandBus::class)->dispatch(Command\CheckOutUser::fromBuildingIdAndUsername(
            $buildingId,
            $request->getParsedBody()['username']
        ));

        return $response->withAddedHeader('Location', '/building/' . $buildingId->toString());
    });

    $app->pipeDispatchMiddleware();

    $whoops->register();
    $app->run();
});