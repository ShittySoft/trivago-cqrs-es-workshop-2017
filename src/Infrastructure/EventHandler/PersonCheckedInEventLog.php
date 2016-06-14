<?php

declare(strict_types=1);

namespace Building\Infrastructure\EventHandler;

use Building\Domain\DomainEvent\PersonCheckedIn;

final class PersonCheckedInEventLog
{
    public function __invoke(PersonCheckedIn $event)
    {
        error_log(
            'New Event happens => ' . get_class($event) .
            "\nUsername: " . $event->username() . "\n",
            3,
            'php://STDERR'
        );
    }
}
