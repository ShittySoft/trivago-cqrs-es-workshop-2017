<?php

declare(strict_types=1);

namespace Building\Domain;

interface UserBlacklistInterface
{
    public function isBlacklisted(string $username) : bool;
}
