<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ObjectBelongsToModelRule implements Rule
{
    public function __construct(
        protected mixed $model,
        protected string $relation,
        protected string $modelName,
        protected bool $create = false,
        protected string $field = 'id'
    ) {
    }

    public function passes($attribute, $value): bool
    {
        if (! $this->model) {
            return false;
        }

        $belongs = $this->model->{$this->relation}()->where($this->field, $value)->exists();

        return $this->create ? ! $belongs : $belongs;
    }

    public function message(): string
    {
        if ($this->create) {
            return 'Este(a) :attribute ya pertenece a este(a) '.$this->modelName.'.';
        }

        return 'Este(a) :attribute no pertenece a este(a) '.$this->modelName.'.';
    }
}
