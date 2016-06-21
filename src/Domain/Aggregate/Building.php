<?php

namespace Building\Domain\Aggregate;

use Assert\Assertion;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\PersonCheckedIn;
use Building\Domain\DomainEvent\PersonCheckedOut;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var string[]
     */
    private $peopleInTheBuilding  = [];

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

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        Assertion::false(in_array($username, $this->peopleInTheBuilding, true), 'Person is already checked in');

        $this->recordThat(PersonCheckedIn::occur(
            $this->aggregateId(),
            [
                'username' => $username,
            ]
        ));
    }

    public function checkOutUser(string $username)
    {
        Assertion::inArray($username, $this->peopleInTheBuilding, 'Person is not in the building');

        $this->recordThat(PersonCheckedOut::occur(
            $this->aggregateId(),
            [
                'username' => $username,
            ]
        ));
    }

    public function whenPersonCheckedIn(PersonCheckedIn $event)
    {
        $key = array_search($event->username(), $this->peopleInTheBuilding, true);

        unset($this->peopleInTheBuilding[$key]);

        $this->peopleInTheBuilding[] = $event->username();
    }

    public function whenPersonCheckedOut(PersonCheckedOut $event)
    {
        $key = array_search($event->username(), $this->peopleInTheBuilding, true);

        unset($this->peopleInTheBuilding[$key]);
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
