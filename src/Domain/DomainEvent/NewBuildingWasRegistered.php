<?php

declare(strict_types=1);

namespace Hanoi\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;

final class NewBuildingWasRegistered extends AggregateChanged
{
    public function name() : string
    {
        return $this->payload['name'];
    }
}
