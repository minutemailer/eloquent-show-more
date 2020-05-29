<?php

declare(strict_types=1);

namespace Minutemailer\EloquentShowMore;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ShowMoreBuilder extends Builder
{
    /**
     * Execute the query as a "select" statement.
     *
     * @param  array|string  $columns
     * @return Collection|static[]
     */
    public function get($columns = ['*'])
    {
        $builder = $this->applyScopes();

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded, which will solve the
        // n+1 query issue for the developers to avoid running a lot of queries.
        if (count($models = $builder->getModels($columns)) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return new ShowMoreCollection($models, $builder);
    }
}
