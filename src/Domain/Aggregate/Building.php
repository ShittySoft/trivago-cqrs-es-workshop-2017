<?php

namespace Hanoi\Domain\Aggregate;

use Assert\Assertion;
use Hanoi\Domain\DomainEvent\PersonCheckedIn;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    private $checkedIn  = [];
    private $checkedOut = [
        'malukenho',
        'ocramius'
    ];

    /**
     * @var Uuid
     */
    private $uuid;

    public static function new() : self
    {
        $self = new self();

        $self->uuid = Uuid::uuid4();

        return $self;
    }

    public function checkInUser(string $username)
    {
        Assertion::inArray($username, $this->checkedOut);

        $this->recordThat(PersonCheckedIn::occur(
            $this->aggregateId(),
            [
                'username' => $username,
            ]
        ));
    }

    public function checkOutUser(string $username)
    {
    }

    public function whenPersonCheckedIn(PersonCheckedIn $event)
    {
        $key = array_search($event->username(), $this->checkedOut, true);

        unset($this->checkedOut[$key]);

        $this->checkedIn[] = $event->username();
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id() : string
    {
        return $this->aggregateId();
    }
}
