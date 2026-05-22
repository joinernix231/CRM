<?php

namespace App\Repositories\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class OrderByCriteria implements CriteriaInterface
{
    public function __construct(
        private string $column,
        private string $direction = 'asc'
    ) {
    }

    public function apply($model, RepositoryInterface $repository)
    {
        return $model->orderBy($this->column, $this->direction);
    }
}
