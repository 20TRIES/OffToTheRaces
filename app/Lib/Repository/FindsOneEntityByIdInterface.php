<?php

namespace App\Lib\Repository;

use Illuminate\Database\Eloquent\Model;

interface FindsOneEntityByIdInterface
{
    /**
     * Finds a model by its id.
     *
     * @param mixed $id
     * @return Model
     */
    public function findOneById($id): Model;
}
