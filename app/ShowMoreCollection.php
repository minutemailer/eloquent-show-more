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

        if (!$model->incrementing && !$model->usesTimestamps()) {
            throw new \InvalidArgumentException(\sprintf('The model needs to have incrementing id or use the trait %s', HasTimestamps::class));
        }

        $limit = $this->count();

        $columnsToFetch = array_keys($model->getAttributes());

        $column = $this->getColumn();
        $rows = $model->newModelQuery()->where($column, '>', $this->max($column))->take($limit + 1)->get($columnsToFetch);

        return [
            'hasMore' => $rows->count() > $limit,
            'data' => $rows->splice(0, $limit),
        ];
    }

    private function getColumn(): string
    {
        /** @var Model $model */
        $model = $this->first();

        if ($model->incrementing) {
            return $model->getKeyName();
        }

        return $model->getCreatedAtColumn();
    }

    public function hasMore(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return true;
    }
}
