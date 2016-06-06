<?php

declare(strict_types=1);

namespace BuildingTest\Domain\DomainEvent;

use Building\Domain\DomainEvent\PersonCheckedOut;
use Rhumsaa\Uuid\Uuid;

/**
 * Tests for {@see \Building\Domain\DomainEvent\PersonCheckedOut}
 *
 * @covers \Building\Domain\DomainEvent\PersonCheckedOut
 */
final class PersonCheckedOutTest extends BaseEventTest
{
    public function testCreation()
    {
        $aggregateId = Uuid::uuid4();
        $domainEvent = PersonCheckedOut::occur(
            $aggregateId->toString(),
            [
                'username' => 'malukenho',
            ]
        );

        self::assertInstanceOf(PersonCheckedOut::class, $domainEvent);
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
                PersonCheckedOut::occur(
                    Uuid::uuid4()->toString(),
                    [
                        'username' => 'malukenho',
                    ]
                )
            ],
        ];
    }
}
