<?php

namespace App\Lib\Repository;

use App\Lib\Repository\Exception\FailedToSaveEntityException;
use Illuminate\Database\Eloquent\Model;

interface PersistsEntitiesInterface
{
    /**
     * Persists one or more entities.
     *
     * @param Model ...$entities
     * @throws FailedToSaveEntityException
     */
    public function persist(Model ...$entities): void;
}
