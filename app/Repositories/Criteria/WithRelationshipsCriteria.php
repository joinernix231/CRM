<?php

namespace App\Repositories\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class WithRelationshipsCriteria implements CriteriaInterface
{
    /**
     * @param  string[]  $relationships
     */
    public function __construct(private array $relationships)
    {
    }

    public function apply($model, RepositoryInterface $repository)
    {
        return $model->with($this->relationships);
    }
}
