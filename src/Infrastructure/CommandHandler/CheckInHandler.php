<?php

declare(strict_types=1);

namespace Building\Infrastructure\CommandHandler;

use Building\Domain\Aggregate\Building;
use Building\Domain\Command\CheckIn;
use Building\Infrastructure\Repository\BuildingRepository;

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
        /** @var Building $build */
        $build = $this->repository->get($command->buildingId());

        $build->checkInUser($command->username());

        $this->repository->addPendingEventsToStream();
    }
}
