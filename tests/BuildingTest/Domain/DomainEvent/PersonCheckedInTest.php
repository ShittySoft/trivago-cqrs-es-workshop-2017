<?php

declare(strict_types=1);

namespace BuildingTest\Domain\DomainEvent;

use Building\Domain\DomainEvent\PersonCheckedIn;
use Rhumsaa\Uuid\Uuid;

/**
 * Tests for {@see \Building\Domain\DomainEvent\PersonCheckedIn}
 *
 * @covers \Building\Domain\DomainEvent\PersonCheckedIn
 */
final class PersonCheckedInTest extends BaseEventTest
{
    public function testCreation()
    {
        $aggregateId = Uuid::uuid4();
        $domainEvent = PersonCheckedIn::occur(
            $aggregateId->toString(),
            [
                'username' => 'malukenho',
            ]
        );

        self::assertInstanceOf(PersonCheckedIn::class, $domainEvent);
        self::assertSame('malukenho', $domainEvent->username());
        self::assertSame($aggregateId->toString(), $domainEvent->aggregateId());
        self::assertEquals($aggregateId, Uuid::fromString($domainEvent->aggregateId()));
    }

    /**
     * {@inheritDoc}
     */
    public function exampleReconstructedDomainEventsProvider() : array
    {
        return [
            [
                PersonCheckedIn::occur(
                    Uuid::uuid4()->toString(),
                    [
                        'username' => 'malukenho',
                    ]
                )
            ],
        ];
    }
}
