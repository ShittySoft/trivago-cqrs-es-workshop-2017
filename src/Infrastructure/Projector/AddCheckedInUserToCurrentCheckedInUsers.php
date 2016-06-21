<?php

declare(strict_types=1);

namespace Building\Infrastructure\Projector;

use Building\Domain\DomainEvent\PersonCheckedIn;

final class AddCheckedInUserToCurrentCheckedInUsers
{
    const FILE_PATH = __DIR__ . '/../../../public/%s.json';

    public function __invoke(PersonCheckedIn $event)
    {
        $filePath = sprintf(self::FILE_PATH, $event->aggregateId());

        file_put_contents(
            $filePath,
            json_encode(array_unique(array_merge($this->getExistingUsers($filePath), [$event->username()])))
        );
    }

    private function getExistingUsers(string $filePath) : array
    {
        if (! file_exists($filePath)) {
            return [];
        }

        $existingUsers = json_decode(file_get_contents($filePath), true);

        if (! is_array($existingUsers)) {
            return [];
        }

        return array_values(array_map('strval', $existingUsers));
    }
}
