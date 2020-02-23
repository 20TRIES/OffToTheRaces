<?php

namespace App\Lib\Repository;

use App\Lib\Repository\Exception\ModelTimestampsDisabledException;
use Illuminate\Database\Eloquent\Collection;
use ReflectionException;

interface GetsLastNEntitiesInterface
{
    /**
     * Get the last "N" entities.
     *
     * @param int $limit
     * @return Collection
     * @throws ModelTimestampsDisabledException
     * @throws ReflectionException
     */
    public function getLastNEntities(int $limit): Collection;
}
