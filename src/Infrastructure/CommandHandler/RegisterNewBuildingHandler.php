<?php

declare(strict_types=1);

namespace Hanoi\Infrastructure\CommandHandler;

use Hanoi\Domain\Aggregate\Building;
use Hanoi\Domain\Command\RegisterNewBuilding;
use Hanoi\Infrastructure\Repository\BuildingRepository;

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
