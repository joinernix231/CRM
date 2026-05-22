<?php

namespace App\Repositories\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Applies query filters in "field|comparator|value" format, separated by ";".
 *
 * Supported comparators: like, =, in
 * Example: search|like|acme;status|=|active
 */
class FiltersCriteria implements CriteriaInterface
{
    private const ALLOWED_COMPARATORS = ['like', '=', 'in'];

    public function __construct(private readonly string $filters)
    {
    }

    public function apply($model, RepositoryInterface $repository)
    {
        $allowedFields = $repository->getFieldsSearchable();

        foreach ($this->parseFilters() as $filter) {
            $parsed = $this->parseFilter($filter);

            if ($parsed === null) {
                continue;
            }

            [$field, $comparator, $value] = $parsed;
            $comparator = $this->normalizeComparator($comparator, $value);

            if (! in_array($field, $allowedFields, true)) {
                continue;
            }

            if (! in_array($comparator, self::ALLOWED_COMPARATORS, true)) {
                continue;
            }

            $model = $this->applyFilter($model, $repository, $field, $comparator, $value);
        }

        return $model;
    }

    private function parseFilters(): array
    {
        return array_values(array_filter(explode(';', $this->filters)));
    }

    private function parseFilter(string $filter): ?array
    {
        $parts = explode('|', $filter, 3);

        if (count($parts) !== 3 || $parts[0] === '' || $parts[1] === '' || $parts[2] === '') {
            return null;
        }

        return $parts;
    }

    private function applyFilter($model, RepositoryInterface $repository, string $field, string $comparator, string $value)
    {
        if ($field === 'search') {
            return $this->applySearchFilter($model, $repository, $comparator, $value);
        }

        return match ($comparator) {
            'like' => $model->where($field, 'like', $this->wrapLike($value)),
            'in' => $model->whereIn($field, explode(',', $value)),
            default => $model->where($field, $value),
        };
    }

    private function applySearchFilter($model, RepositoryInterface $repository, string $comparator, string $value)
    {
        $columns = method_exists($repository, 'getSearchColumns')
            ? $repository->getSearchColumns()
            : [];

        if ($columns === []) {
            return $model;
        }

        $filterValue = $comparator === 'like' ? $this->wrapLike($value) : $value;

        return $model->where(function ($query) use ($columns, $comparator, $filterValue) {
            foreach ($columns as $column) {
                $query->orWhere($column, $comparator === 'like' ? 'like' : '=', $filterValue);
            }
        });
    }

    private function wrapLike(string $value): string
    {
        return '%'.$value.'%';
    }

  /**
     * "is" is for booleans in the legacy format; enum/string fields use "=".
     */
    private function normalizeComparator(string $comparator, string $value): string
    {
        if ($comparator === 'is' && ! in_array($value, ['true', 'false'], true)) {
            return '=';
        }

        return $comparator;
    }
}
