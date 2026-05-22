<?php

namespace App\Repositories\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class WhereFieldCriteria implements CriteriaInterface
{
    public function __construct(
        private string $field,
        private mixed $value = null,
        private string $operator = '='
    ) {
    }

    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->operator === 'in') {
            return $model->whereIn($this->field, $this->value);
        }

        return $model->where($this->field, $this->operator, $this->value);
    }
}
