<?php

declare(strict_types=1);

namespace Hanoi\Infrastructure\CommandHandler;

use Hanoi\Domain\Aggregate\Building;
use Hanoi\Domain\Command\CheckOut;
use Hanoi\Infrastructure\Repository\BuildingRepository;

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
        $build = $this->repository->get();

        $build->checkOutUser($command->username());
    }
}
