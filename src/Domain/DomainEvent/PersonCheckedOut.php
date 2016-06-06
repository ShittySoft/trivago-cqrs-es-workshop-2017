<?php

declare(strict_types=1);

namespace Hanoi\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;

final class PersonCheckedOut extends AggregateChanged
{
    public function username() : string
    {
        return $this->payload['username'];
    }
}
