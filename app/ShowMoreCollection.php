<?php

declare(strict_types=1);

namespace Minutemailer\EloquentShowMore;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class ShowMoreCollection extends Collection
{
    public function showMore(): array
    {
        if (!$this->hasMore()) {
            return [
                'hasMore' => false,
                'data' => new self(),
            ];
        }

        /** @var Model $model */
        $model = $this->first();

        if (!$model->usesTimestamps()) {
            throw new \InvalidArgumentException(\sprintf('The model needs to use the trait %s', HasTimestamps::class));
        }

        $createdAtColumn = $model->getCreatedAtColumn();
        $lastCreated = $this->max($createdAtColumn);
        $limit = $this->count();

        $rows = $this->first()->newModelQuery()->where($createdAtColumn, '>', $lastCreated)->take($limit + 1)->get();

        return [
            'hasMore' => $rows->count() > $limit,
            'data' => $rows->splice(0, $limit),
        ];
    }

    public function hasMore(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return true;
    }
}
