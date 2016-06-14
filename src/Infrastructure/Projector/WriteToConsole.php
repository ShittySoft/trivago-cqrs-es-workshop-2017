<?php

declare(strict_types=1);

namespace Infrastructure\Projector;

use Building\Domain\DomainEvent\PersonCheckedIn;

final class WriteToConsole
{
    public function __invoke(PersonCheckedIn $event)
    {
        $stderr = fopen('php://STDERR', 'w');

        fwrite($stderr, 'Project for ' . get_class($event) . ' was called with username ->' . $event->username() . "\n");

        fclose($stderr);
    }
}
