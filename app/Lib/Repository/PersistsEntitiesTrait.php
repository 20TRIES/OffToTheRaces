<?php

namespace App\Lib\Repository;

use App\Lib\Repository\Exception\FailedToSaveEntityException;
use Illuminate\Database\Eloquent\Model;

trait PersistsEntitiesTrait
{
    /**
     * Persists one or more entities.
     *
     * @param Model ...$entities
     * @throws FailedToSaveEntityException
     */
    public function persist(Model ...$entities): void
    {
        foreach ($entities as $entity) {
            if (false === $entity->save()) {
                throw new FailedToSaveEntityException();
            }
        }
    }
}
