<?php

declare(strict_types=1);

namespace Building\Infrastructure\Repository;

use Building\Domain\Aggregate\Building;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\EventStore;
use Rhumsaa\Uuid\Uuid;

// @TODO move repository to domain, AVOID inheriting from logic at all costs
final class BuildingRepository extends AggregateRepository
{
    public function __construct(EventStore $eventStore)
    {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(Building::class),
            new AggregateTranslator()
        );
    }

    public function add(Building $building)
    {
        $this->addAggregateRoot($building);
    }

    public function get(Uuid $id)
    {
        return $this->getAggregateRoot($id->toString());
    }
}
