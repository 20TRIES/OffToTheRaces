<?php

namespace App\Lib\Repository;

use App\Lib\Repository\Exception\ModelTimestampsDisabledException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait GetsLastNEntitiesTrait
{
    /**
     * @param int $limit
     * @return Builder
     * @throws ModelTimestampsDisabledException
     */
    protected function getLastNEntitiesBuilder(int $limit): Builder
    {
        $entityClass = $this->getModelClassReference();
        $createdAtAttributeName = $entityClass::CREATED_AT;
        if (null === $createdAtAttributeName || (new $entityClass())->timestamps === false) {
            throw new ModelTimestampsDisabledException();
        }
        return $this->newQueryBuilder()->orderBy($createdAtAttributeName, 'DESC')->limit($limit);
    }

    /**
     * @param int $limit
     * @return Collection
     * @throws ModelTimestampsDisabledException
     */
    public function getLastNEntities(int $limit): Collection
    {
        return $this->getLastNEntitiesBuilder($limit)->get();
    }
}
