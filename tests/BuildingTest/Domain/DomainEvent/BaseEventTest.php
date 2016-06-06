<?php

declare(strict_types=1);

namespace BuildingTest\Domain\DomainEvent;

use Prooph\Common\Messaging\DomainMessage;
use Rhumsaa\Uuid\Uuid;

abstract class BaseEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider exampleReconstructedDomainEventsProvider
     *
     * @param DomainMessage $domainEvent
     */
    public function testToAndFromArrayProducesEquivalentObject(DomainMessage $domainEvent)
    {
        self::assertEquals(
            $domainEvent,
            $domainEvent::fromArray($domainEvent->toArray())
        );
    }

    /**
     * @dataProvider exampleReconstructedDomainEventsProvider
     *
     * @param DomainMessage $domainEvent
     */
    public function testEventPayloadIsJsonSerializable(DomainMessage $domainEvent)
    {
        self::assertEquals(
            $domainEvent->payload(),
            json_decode(json_encode($domainEvent->payload()), true)
        );
    }

    /**
     * @dataProvider exampleReconstructedDomainEventsProvider
     *
     * @param DomainMessage $domainEvent
     */
    public function testDomainEventAlwaysHasAnIdentifier(DomainMessage $domainEvent)
    {
        self::assertInstanceOf(Uuid::class, $domainEvent->uuid());
    }

    /**
     * @dataProvider exampleReconstructedDomainEventsProvider
     *
     * @param DomainMessage $domainEvent
     */
    public function testDomainEventIsAnEvent(DomainMessage $domainEvent)
    {
        self::assertSame(DomainMessage::TYPE_EVENT, $domainEvent->messageType());
    }

    /**
     * @dataProvider exampleReconstructedDomainEventsProvider
     *
     * @param DomainMessage $domainEvent
     */
    public function testDomainAlwaysHasACreatedAt(DomainMessage $domainEvent)
    {
        self::assertInstanceOf(\DateTimeImmutable::class, $domainEvent->createdAt());
    }

    /**
     * @dataProvider exampleReconstructedDomainEventsProvider
     *
     * @param DomainMessage $domainEvent
     */
    public function testDomainEventNamesAreNotTranslated(DomainMessage $domainEvent)
    {
        self::assertSame(get_class($domainEvent), $domainEvent->messageName());
    }
    /**
     *
     * @dataProvider exampleReconstructedDomainEventsProvider
     *
     * @param DomainMessage $domainEvent
     */
    public function testDomainEventAlwaysHasAnAssignedVersion(DomainMessage $domainEvent)
    {
        self::assertInternalType('int', $domainEvent->version());
    }

    /**
     * @return DomainMessage[]
     */
    abstract public function exampleReconstructedDomainEventsProvider() : array;
}
