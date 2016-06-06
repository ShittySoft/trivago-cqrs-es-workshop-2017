<?php

declare(strict_types=1);

namespace BuildingTest\Domain\Command;

use Building\Domain\Command\RegisterNewBuilding;

/**
 * Tests for {@see \Building\Domain\Command\RegisterNewBuilding}.
 *
 * @covers \Building\Domain\Command\RegisterNewBuilding
 */
final class RegisterNewBuildingTest extends BaseCommandTest
{
    public function testFromBuildingIdAndUserName()
    {
        $command = RegisterNewBuilding::fromName($name = 'ABC Company');
        self::assertInstanceOf(RegisterNewBuilding::class, $command);
        self::assertSame('ABC Company', $command->name());
    }

    /**
     * {@inheritDoc}
     */
    public function exampleReconstructedCommandsProvider() : array
    {
        return [
            [
                RegisterNewBuilding::fromName($name = 'ABC Company'),
            ],
        ];
    }
}
