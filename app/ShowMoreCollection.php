<?php

declare(strict_types=1);

namespace Minutemailer\EloquentShowMore;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class ShowMoreCollection extends Collection
{
    /**
     * @var ShowMoreBuilder
     */
    private $builder;

    /**
     * @var array $items
     * @var ShowMoreBuilder $builder
     */
    public function __construct($items = [], $builder = null)
    {
        parent::__construct($items);
        $this->builder = $builder;
    }

    public function showMore(): array
    {
        if ($this->isEmpty()) {
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
        $rows = $this->builder->where($column, '>', $this->max($column))->take($limit + 1)->get($columnsToFetch);

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
}
