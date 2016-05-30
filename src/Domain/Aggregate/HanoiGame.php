<?php

declare(strict_types=1);

namespace Hanoi\Domain\Aggregate;

use Hanoi\Domain\DomainEvent\NewGameWasCreated;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class HanoiGame extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var int
     */
    private $quantityOfPieces;

    public static function newGameFromQuantityOfPieces(int $quantity) : self
    {
        $aggregate = new self();

        $aggregate->uuid             = Uuid::uuid4();
        $aggregate->quantityOfPieces = $quantity;

        $aggregate->recordThat(NewGameWasCreated::fromQuantityOfPieces($quantity));
    }

    public function movePieceToColumn()
    {
        // TODO: implement on the class
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }
}
