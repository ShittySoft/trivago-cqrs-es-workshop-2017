<?php

declare(strict_types=1);

namespace Hanoi\Infrastructure\Middleware;

use Assert\Assertion;
use Hanoi\Domain\Command\CreateNewGame;
use Prooph\ServiceBus\CommandBus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Stratigility\MiddlewareInterface;

final class Solve implements MiddlewareInterface
{
    const PATH = '/solve';

    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Assert\AssertionFailedException
     * @throws \InvalidArgumentException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $quantityOfPieces = $request->getParsedBody()['pieces'];

        Assertion::notNull($quantityOfPieces);

        $commandBus = $this->commandBus;
        $commandBus->dispatch(CreateNewGame::fromQuantityOfPieces((int) $quantityOfPieces));

        return $response->withAddedHeader('Location', '/result');
    }
}
