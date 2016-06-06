<?php

declare(strict_types=1);

namespace Building\Infrastructure\CommandHandler;

use Building\Domain\Aggregate\Building;
use Building\Domain\Command\CheckOut;
use Building\Infrastructure\Repository\BuildingRepository;

final class CheckOutHandler
{
    /**
     * @var BuildingRepository
     */
    private $repository;

    public function __construct(BuildingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CheckOut $command)
    {
        /* @var Building $build */
        $build = $this->repository->get($command->buildingId());

        $build->checkOutUser($command->username());

        $this->repository->addPendingEventsToStream();
    }
}
