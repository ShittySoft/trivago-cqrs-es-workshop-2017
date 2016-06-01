<?php

declare(strict_types=1);

namespace Hanoi\Infrastructure\CommandHandler;

use Hanoi\Domain\Aggregate\Building;
use Hanoi\Domain\Command\CheckIn;
use Hanoi\Infrastructure\Repository\BuildingRepository;

final class CheckInHandler
{
    /**
     * @var BuildingRepository
     */
    private $repository;

    public function __construct(BuildingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CheckIn $command)
    {
        $build = Building::new();

        $build->checkInUser($command->username());

        $this->repository->add($build);
    }
}
