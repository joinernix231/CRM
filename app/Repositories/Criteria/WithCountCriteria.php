<?php

namespace App\Repositories\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class WithCountCriteria implements CriteriaInterface
{
    /**
     * @param  string[]  $relationships
     */
    public function __construct(private array $relationships)
    {
    }

    public function apply($model, RepositoryInterface $repository)
    {
        return $model->withCount($this->relationships);
    }
}
