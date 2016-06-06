<?php

declare(strict_types=1);

namespace BuildingTest\Domain\DomainEvent;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Prooph\Common\Messaging\DomainMessage;
use Rhumsaa\Uuid\Uuid;

/**
 * Tests for {@see \Building\Domain\DomainEvent\NewBuildingWasRegistered}
 *
 * @covers \Building\Domain\DomainEvent\NewBuildingWasRegistered
 */
final class NewBuildingWasRegisteredTest extends BaseEventTest
{
    public function testCreation()
    {
        $aggregateId = Uuid::uuid4();
        $domainEvent = NewBuildingWasRegistered::occur(
            $aggregateId->toString(),
            [
                'name' => 'ABC Building',
            ]
        );

        self::assertInstanceOf(NewBuildingWasRegistered::class, $domainEvent);
        self::assertSame('ABC Building', $domainEvent->name());
        self::assertSame($aggregateId->toString(), $domainEvent->aggregateId());
        self::assertEquals($aggregateId, Uuid::fromString($domainEvent->aggregateId()));
    }

    /**
     * @return DomainMessage[]
     */
    public function exampleReconstructedDomainEventsProvider() : array
    {
        return [
            [
                NewBuildingWasRegistered::occur(
                    Uuid::uuid4()->toString(),
                    [
                        'name' => 'ABC Building',
                    ]
                )
            ],
        ];
    }
}
