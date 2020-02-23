<?php

namespace App\Lib\Repository;

use Illuminate\Database\Eloquent\Builder;

abstract class Repository
{
    /**
     * Gets the class reference for the model that a repository should use.
     *
     * @return string
     */
    abstract public function getModelClassReference(): string;

    /**
     * Gets a new query builder instance.
     *
     * @return Builder
     */
    public function newQueryBuilder(): Builder
    {
        return call_user_func([$this->getModelClassReference(), 'query']);
    }
}
