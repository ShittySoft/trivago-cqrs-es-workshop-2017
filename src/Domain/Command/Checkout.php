<?php

declare(strict_types=1);

namespace Hanoi\Domain\Command;

use Prooph\Common\Messaging\Command;

final class CheckOut extends Command
{
    /**
     * @var \DateTimeImmutable
     */
    private $currentTime;

    /**
     * @var string
     */
    private $username;

    private function __construct(string $username)
    {
        $this->init();

        $this->currentTime = new \DateTimeImmutable();
        $this->username    = $username;
    }

    public static function fromUserName(string $username) : self
    {
        return new static($username);
    }

    public function username() : string
    {
        return $this->username;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'username'    => $this->username,
            'currentTime' => $this->currentTime->format('U'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->currentTime = \DateTimeImmutable::createFromFormat('U', $payload['currentTime']);
        $this->username    = $payload['username'];
    }
}
