<?php

declare(strict_types=1);

namespace BuildingTest\Domain\Aggregate;

use Building\Domain\Aggregate\Building;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\PersonCheckedIn;
use Building\Domain\DomainEvent\PersonCheckedOut;
use Prooph\Common\Messaging\DomainMessage;
use Prooph\EventSourcing\EventStoreIntegration\AggregateRootDecorator;
use Rhumsaa\Uuid\Uuid;

/**
 * Tests for {@see \Building\Domain\Aggregate\Building}
 *
 * @covers \Building\Domain\Aggregate\Building
 */
final class BuildingTest extends \PHPUnit_Framework_TestCase
{
    public function testStartFromBuildingName()
    {
        $building = Building::new('I Building');

        self::assertInstanceOf(Building::class, $building);

        $buildingId = $building->id();

        self::assertInstanceOf(Uuid::class, Uuid::fromString($buildingId));
        self::assertEquals(
            $buildingId,
            AggregateRootDecorator::newInstance()->extractAggregateId($building)
        );

        $this->assertEvents(
            [
                NewBuildingWasRegistered::occur(
                    $buildingId,
                    [
                        'name' => 'I Building',
                    ]
                )
            ],
            $building
        );
    }

    public function testCheckIn()
    {
        $building = Building::new('I Building');
        self::assertInstanceOf(Building::class, $building);

        $buildingId = $building->id();

        self::assertInstanceOf(Uuid::class, Uuid::fromString($buildingId));
        self::assertEquals(
            $buildingId,
            AggregateRootDecorator::newInstance()->extractAggregateId($building)
        );

        $building->checkInUser('malukenho');

        $this->assertEvents(
            [
                NewBuildingWasRegistered::occur(
                    $buildingId,
                    [
                        'name' => 'I Building',
                    ]
                ),
                PersonCheckedIn::occur(
                    $buildingId,
                    [
                        'username' => 'malukenho',
                    ]
                )
            ],
            $building
        );
    }
    public function testCheckOut()
    {
        $building = Building::new('I Building');
        self::assertInstanceOf(Building::class, $building);

        $buildingId = $building->id();

        self::assertInstanceOf(Uuid::class, Uuid::fromString($buildingId));
        self::assertEquals(
            $buildingId,
            AggregateRootDecorator::newInstance()->extractAggregateId($building)
        );

        $building->checkInUser('malukenho');
        $building->checkOutUser('malukenho');

        $this->assertEvents(
            [
                NewBuildingWasRegistered::occur(
                    $buildingId,
                    [
                        'name' => 'I Building',
                    ]
                ),
                PersonCheckedIn::occur(
                    $buildingId,
                    [
                        'username' => 'malukenho',
                    ]
                ),
                PersonCheckedOut::occur(
                    $buildingId,
                    [
                        'username' => 'malukenho',
                    ]
                )
            ],
            $building
        );
    }

    /**
     * @param DomainMessage[] $expectedEvents
     * @param Building        $aggregate
     */
    private function assertEvents(array $expectedEvents, Building $aggregate)
    {
        /* @var $recordedEvents DomainMessage[] */
        $recordedEvents = AggregateRootDecorator::newInstance()->extractRecordedEvents($aggregate);

        self::assertEmpty(AggregateRootDecorator::newInstance()->extractRecordedEvents($aggregate));
        self::assertCount(count($expectedEvents), $recordedEvents);

        foreach ($expectedEvents as $key => $event) {
            self::assertInstanceOf(get_class($event), $recordedEvents[$key]);
            self::assertEquals($event->payload(), $recordedEvents[$key]->payload());
        }
    }
}
