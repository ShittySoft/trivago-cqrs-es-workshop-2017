<?php

declare(strict_types=1);

namespace Building\Domain\Repository;

use Building\Domain\Aggregate\Building;
use Rhumsaa\Uuid\Uuid;

interface BuildingRepositoryInterface
{
    public function add(Building $building);
    public function get(Uuid $id) : Building;
}
