<?php

declare(strict_types=1);

namespace Minutemailer\EloquentShowMore;

trait ShowMoreTrait
{
    public function newCollection(array $models = [])
    {
        return new ShowMoreCollection($models);
    }
}
