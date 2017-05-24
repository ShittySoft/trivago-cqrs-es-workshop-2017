<?php

declare(strict_types=1);

namespace BuildingTest\Context;

use Assert\Assertion;
use Behat\Behat\Context\Context;
use Building\Domain\Aggregate\Building;
use Building\Domain\DomainEvent\CheckInAnomalyDetected;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIn;
use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class BuildingContext implements Context
{
    /**
     * @var Uuid
     */
    private $buildingId;

    /**
     * @var AggregateChanged[]
     */
    private $pastEvents = [];

    /**
     * @var Building|null
     */
    private $building;

    public function __construct()
    {
        $this->buildingId = Uuid::uuid4();
    }

    /**
     * @Given a building was registered
     */
    public function aBuildingWasRegistered()
    {
        $this->pastEvents[] = NewBuildingWasRegistered::fromBuildingIdAndName(
            $this->buildingId,
            'Trivago ES office'
        );
    }

    /**
     * @Given a user checked into the building
     */
    public function aUserCheckedIntoTheBuilding()
    {
        $this->pastEvents[] = UserCheckedIn::fromBuildingIdAndUsername(
            $this->buildingId,
            'Marco'
        );
    }

    /**
     * @When the user checks into the building
     *
     * @throws \ReflectionException
     */
    public function theUserChecksIntoTheBuilding()
    {
        $this->getOrCreateBuilding()->checkInUser('Marco');
    }

    /**
     * @Then an check-in anomaly was detected
     */
    public function aCheckInAnomalyWasDetected()
    {
        $recordedEvents = $this->getRecordedEvents();

        Assertion::count($recordedEvents, 2);
        Assertion::isInstanceOf($recordedEvents[0], UserCheckedIn::class);
        Assertion::isInstanceOf($recordedEvents[1], CheckInAnomalyDetected::class);
    }

    /**
     * @return AggregateChanged[]
     *
     * @throws \ReflectionException
     */
    private function getRecordedEvents() : array
    {
        $getEvents = new \ReflectionMethod(
            Building::class,
            'popRecordedEvents'
        );

        $getEvents->setAccessible(true);

        return $getEvents->invoke($this->getOrCreateBuilding());
    }

    private function getOrCreateBuilding() : Building
    {
        return $this->building
            ?? $this->building = (function (array $events) : Building {
            return Building::reconstituteFromHistory(new \ArrayIterator($events));
        })
            ->bindTo(null, Building::class)
            ->__invoke($this->pastEvents);
    }
}
