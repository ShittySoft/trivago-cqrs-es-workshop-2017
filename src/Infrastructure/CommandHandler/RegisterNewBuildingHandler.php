<?php

declare(strict_types=1);

namespace Building\Infrastructure\CommandHandler;

use Building\Domain\Aggregate\Building;
use Building\Domain\Command\RegisterNewBuilding;
use Building\Infrastructure\Repository\BuildingRepository;

final class RegisterNewBuildingHandler
{
    /**
     * @var BuildingRepository
     */
    private $repository;

    public function __construct(BuildingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RegisterNewBuilding $command)
    {
        $build = Building::new($command->name());

        $this->repository->add($build);
    }
}
