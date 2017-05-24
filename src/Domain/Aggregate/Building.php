<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\CheckInAnomalyDetected;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIn;
use Building\Domain\DomainEvent\UserCheckedOut;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array<string, null>
     */
    private $checkedInUsers = [];

    public static function new(string $name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::fromBuildingIdAndName(
            Uuid::uuid4(),
            $name
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        $anomaly = \array_key_exists($username, $this->checkedInUsers);

        $this->recordThat(UserCheckedIn::fromBuildingIdAndUsername(
            $this->uuid,
            $username
        ));

        if ($anomaly) {
            $this->recordThat(CheckInAnomalyDetected::fromBuildingIdAndUsername(
                $this->uuid,
                $username
            ));
        }
    }

    public function checkOutUser(string $username)
    {
        $anomaly = ! \array_key_exists($username, $this->checkedInUsers);

        $this->recordThat(UserCheckedOut::fromBuildingIdAndUsername(
            $this->uuid,
            $username
        ));

        if ($anomaly) {
            $this->recordThat(CheckInAnomalyDetected::fromBuildingIdAndUsername(
                $this->uuid,
                $username
            ));
        }
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    public function whenUserCheckedIn(UserCheckedIn $event)
    {
        $this->checkedInUsers[$event->username()] = null;
    }

    public function whenUserCheckedOut(UserCheckedOut $event)
    {
        unset($this->checkedInUsers[$event->username()]);
    }

    public function whenCheckInAnomalyDetected(CheckInAnomalyDetected $event)
    {
        // nothing
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
