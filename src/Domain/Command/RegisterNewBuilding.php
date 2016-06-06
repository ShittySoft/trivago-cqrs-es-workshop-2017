<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;

final class RegisterNewBuilding extends Command
{
    /**
     * @var string
     */
    private $name;

    private function __construct(string $name)
    {
        $this->init();

        $this->name = $name;
    }

    public static function fromName(string $name) : self
    {
        return new self($name);
    }

    public function name() : string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'name' => $this->name,
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->name = $payload['name'];
    }
}
