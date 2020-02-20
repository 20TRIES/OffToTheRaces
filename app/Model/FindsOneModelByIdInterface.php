<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

interface FindsOneModelByIdInterface
{
    /**
     * Finds a model by its id.
     *
     * @param mixed $id
     * @return Model
     */
    public function findOneById($id): Model;
}
