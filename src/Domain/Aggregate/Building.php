<?php

namespace Hanoi\Domain\Aggregate;

use Assert\Assertion;
use Hanoi\Domain\DomainEvent\NewBuildingWasRegistered;
use Hanoi\Domain\DomainEvent\PersonCheckedIn;
use Hanoi\Domain\DomainEvent\PersonCheckedOut;
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

    /**
     * @var string
     */
    private $name;

    public static function new($name) : self
    {
        $self = new self();
        $self->uuid = Uuid::uuid4();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) $self->uuid,
            [
                'name' => $name
            ]
        ));

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
        Assertion::inArray($username, $this->checkedIn);

        $this->recordThat(PersonCheckedOut::occur(
            $this->aggregateId(),
            [
                'username' => $username,
            ]
        ));
    }

    public function whenPersonCheckedIn(PersonCheckedIn $event)
    {
        $key = array_search($event->username(), $this->checkedOut, true);

        unset($this->checkedOut[$key]);

        $this->checkedIn[] = $event->username();
    }

    public function whenPersonCheckedOut(PersonCheckedOut $event)
    {
        $key = array_search($event->username(), $this->checkedIn, true);

        unset($this->checkedIn[$key]);

        $this->checkedOut[] = $event->username();
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
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
