<?php

declare(strict_types=1);

namespace BuildingTest\Domain\Command;

use Building\Domain\Command\CheckOut;
use Rhumsaa\Uuid\Uuid;

/**
 * Tests for {@see \Building\Domain\Command\CheckOut}.
 *
 * @covers \Building\Domain\Command\CheckOut
 */
final class CheckOutTest extends BaseCommandTest
{
    public function testFromBuildingIdAndUserName()
    {
        $buildingId = Uuid::uuid4();
        $username   = 'malukenho';

        $command = CheckOut::fromBuildingIdAndUserName($buildingId, $username);
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
                CheckOut::fromBuildingIdAndUserName(Uuid::uuid4(), 'ocramius'),
            ],
        ];
    }
}
