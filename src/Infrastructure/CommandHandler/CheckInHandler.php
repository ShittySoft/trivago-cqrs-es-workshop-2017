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

        // @TODO do we do it like this, or
        $this->repository->addPendingEventsToStream();
        // @TODO this:
        //$this->repository->addAggregateRoot($build);
    }
}
