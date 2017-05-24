<?php

declare(strict_types=1);

namespace Building\Infrastructure;

use Building\Domain\UserBlacklistInterface;

final class ArrayBlacklist implements UserBlacklistInterface
{
    /**
     * @var \string[]
     */
    private $users;

    public function __construct(string ...$users)
    {
        $this->users = $users;
    }

    public function isBlacklisted(string $username) : bool
    {
        return in_array($username, $this->users, true);
    }
}
