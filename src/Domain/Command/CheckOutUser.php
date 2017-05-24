<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

final class CheckOutUser extends Command
{
    /**
     * @var Uuid
     */
    private $buildingId;
    /**
     * @var string
     */
    private $username;

    private function __construct(Uuid $buildingId, string $username)
    {
        $this->init();

        $this->buildingId = $buildingId;
        $this->username = $username;
    }

    public static function fromBuildingIdAndUsername(Uuid $buildingId, string $username) : self
    {
        return new self($buildingId, $username);
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
            'username' => $this->username,
            'buildingId' => $this->buildingId->toString(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->username = $payload['username'];
        $this->buildingId = Uuid::fromString($payload['buildingId']);
    }
}
