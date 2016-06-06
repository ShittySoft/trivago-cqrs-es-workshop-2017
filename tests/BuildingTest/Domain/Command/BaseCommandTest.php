<?php

declare(strict_types=1);

namespace BuildingTest\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

abstract class BaseCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider exampleReconstructedCommandsProvider
     *
     * @param Command $command
     */
    public function testToAndFromArrayProducesEquivalentObject(Command $command)
    {
        self::assertEquals(
            $command,
            $command::fromArray($command->toArray())
        );
    }

    /**
     * @dataProvider exampleReconstructedCommandsProvider
     *
     * @param Command $command
     */
    public function testCommandPayloadIsJsonSerializable(Command $command)
    {
        self::assertEquals(
            $command->payload(),
            json_decode(json_encode($command->payload()), true)
        );
    }

    /**
     * @dataProvider exampleReconstructedCommandsProvider
     *
     * @param Command $command
     */
    public function testDomainEventAlwaysHasAnIdentifier(Command $command)
    {
        self::assertInstanceOf(Uuid::class, $command->uuid());
    }

    /**
     * @dataProvider exampleReconstructedCommandsProvider
     *
     * @param Command $command
     */
    public function testDomainEventIsAnEvent(Command $command)
    {
        self::assertSame(Command::TYPE_COMMAND, $command->messageType());
    }

    /**
     * @dataProvider exampleReconstructedCommandsProvider
     *
     * @param Command $command
     */
    public function testDomainAlwaysHasACreatedAt(Command $command)
    {
        self::assertInstanceOf(\DateTimeImmutable::class, $command->createdAt());
    }

    /**
     * @dataProvider exampleReconstructedCommandsProvider
     *
     * @param Command $command
     */
    public function testDomainEventNamesAreNotTranslated(Command $command)
    {
        self::assertSame(get_class($command), $command->messageName());
    }
    /**
     *
     * @dataProvider exampleReconstructedCommandsProvider
     *
     * @param Command $command
     */
    public function testDomainEventAlwaysHasAnAssignedVersion(Command $command)
    {
        self::assertInternalType('int', $command->version());
    }

    /**
     * @return Command[]
     */
    abstract public function exampleReconstructedCommandsProvider() : array;
}
