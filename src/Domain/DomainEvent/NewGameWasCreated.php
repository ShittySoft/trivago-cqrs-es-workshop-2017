<?php

namespace Hanoi\Domain\DomainEvent;

use Prooph\Common\Messaging\DomainMessage;

final class NewGameWasCreated extends DomainMessage
{
    /**
     * @var int
     */
    private $quantityOfPieces;

    private function __construct($quantity)
    {
        $this->init();

        $this->quantityOfPieces = $quantity;
    }

    public static function fromQuantityOfPieces(int $quantity) : self
    {
        return new self($quantity);
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
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

    /**
     * {@inheritDoc}
     */
    public function messageType() : string
    {
        return self::TYPE_EVENT;
    }
}
