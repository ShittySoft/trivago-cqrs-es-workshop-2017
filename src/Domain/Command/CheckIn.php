<?php

declare(strict_types=1);

namespace Hanoi\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

final class CheckIn extends Command
{
    /**
     * @var \DateTimeImmutable
     */
    private $currentTime;

    /**
     * @var string
     */
    private $username;

    /**
     * @var Uuid
     */
    private $buildingId;

    private function __construct(Uuid $buildingId, string $username)
    {
        $this->init();

        $this->currentTime = new \DateTimeImmutable();
        $this->username    = $username;
        $this->buildingId  = $buildingId;
    }

    public static function fromBuildingIdAndUserName(Uuid $buildingId, string $username) : self
    {
        return new static($buildingId, $username);
    }

    public function username() : string
    {
        return $this->username;
    }

    public function buildingId() : Uuid
    {
        return $this->buildingId;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'username'    => $this->username,
            'currentTime' => $this->currentTime->format('U'),
            'buildingId'  => $this->buildingId->toString(),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function setPayload(array $payload)
    {
        $this->currentTime = \DateTimeImmutable::createFromFormat('U', $payload['currentTime']);
        $this->username    = $payload['username'];
        $this->buildingId  = Uuid::fromString($payload['buildingId']);
    }
}
