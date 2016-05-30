<?php

declare(strict_types=1);

namespace Hanoi\Domain\Command;

use Prooph\Common\Messaging\Command;

final class CreateNewGame extends Command
{
    /**
     * @var int
     */
    private $quantityOfPieces;

    private function __construct(int $quantityOfPieces)
    {
        $this->init();

        $this->quantityOfPieces = $quantityOfPieces;
    }

    public static function fromQuantityOfPieces(int $quantityOfPieces) : self
    {
        return new self($quantityOfPieces);
    }

    /**
     * {@inheritDoc}
     */
    public function payload()
    {
        return [
            'quantityOfPieces' => $this->quantityOfPieces,
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->quantityOfPieces = $payload['quantityOfPieces'];
    }
}
