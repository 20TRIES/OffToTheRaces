<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Builder;

abstract class Repository
{
    /**
     * Gets a new query builder instance.
     *
     * @return Builder
     */
    abstract static function newQueryBuilder(): Builder;
}
