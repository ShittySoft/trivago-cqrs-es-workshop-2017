<?php

namespace BuildingTest\Domain\Command;

use Building\Domain\Command\CheckIn;
use Rhumsaa\Uuid\Uuid;

/**
 * Tests for {@see \Building\Domain\Command\CheckIn}.
 *
 * @covers \Building\Domain\Command\CheckIn
 */
final class CheckInTest extends BaseCommandTest
{
    public function testFromBuildingIdAndUserName()
    {
        $buildingId = Uuid::uuid4();
        $username   = 'malukenho';

        $command = CheckIn::fromBuildingIdAndUserName($buildingId, $username);
        self::assertInstanceOf(Uuid::class, $command->buildingId());
        self::assertSame($buildingId, $command->buildingId());
        self::assertSame($buildingId->toString(), $command->buildingId()->toString());
        self::assertSame($username, $command->username());
    }

    /**
     * {@inheritDoc}
     */
    public function exampleReconstructedCommandsProvider() : array
    {
        return [
            [
                CheckIn::fromBuildingIdAndUserName(Uuid::uuid4(), 'ocramius'),
            ],
        ];
    }
}
