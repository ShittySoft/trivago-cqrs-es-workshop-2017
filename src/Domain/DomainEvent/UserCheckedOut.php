<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class UserCheckedOut extends AggregateChanged
{
    public static function fromBuildingIdAndUsername(
        Uuid $buildingId,
        string $username
    ) : self {
        return self::occur((string) $buildingId, ['username' => $username]);
    }

    public function username() : string
    {
        return $this->payload['username'];
    }
}
