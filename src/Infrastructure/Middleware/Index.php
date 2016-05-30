<?php

declare(strict_types=1);

namespace Hanoi\Infrastructure\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Stratigility\MiddlewareInterface;

final class Index implements MiddlewareInterface
{
    const PATH = '/';

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $response->getBody()->write('
<form action="/solve" method="post">
    <input type="range" name="pieces" min="1" max="10" placeholder="Quantity of pieces"/>
    <output for="pieces" onforminput="value = pieces.valueAsNumber;"></output>
    <button> Solve it!</button>
</form>
');

        return $response;
    }
}
