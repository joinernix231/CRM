<?php

namespace App\Http\Requests;

use App\Support\ResponseUtil;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class APIRequest extends FormRequest
{
    public function failedExists(string $modelName): void
    {
        throw new HttpResponseException(
            response()->json(ResponseUtil::makeError('Este '.$modelName.' no se ha encontrado.'), 400)
        );
    }

    public function addParametersToRequest(array $params): void
    {
        $parameters = $this->all();

        foreach ($params as $key => $param) {
            $parameters[$key] = $param;
        }

        $this->replace($parameters);
    }

    public function messages(): array
    {
        return [
            'email' => 'El email no es válido.',
            'exists' => 'Este(a) :attribute no existe.',
            'in' => 'El campo :attribute debe ser uno de: :values.',
            'integer' => 'El campo :attribute debe ser numérico.',
            'max' => 'El campo :attribute no debe ser mayor a :max caracteres.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'required' => 'El campo :attribute es requerido.',
            'string' => 'El campo :attribute debe ser texto.',
            'unique' => 'Este(a) :attribute ya ha sido tomado(a).',
            'confirmed' => 'La confirmación de :attribute no coincide.',
            'max_digits' => 'El :attribute no debe ser mayor a :max digitos.',
        ];
    }

    public function getData($repository, $id = null)
    {
        $id = $id ?: $this->route('id');

        return $repository->findWithoutFail($id);
    }

    public function existsRule(string $table, string $column = 'id', bool $withSoftDeletes = false)
    {
        return Rule::exists($table, $column)->where(function ($query) use ($withSoftDeletes) {
            if ($withSoftDeletes) {
                $query->whereNull('deleted_at');
            }
        });
    }

    public function uniqueRule(string $table, string $column = 'id', bool $withSoftDeletes = false)
    {
        return Rule::unique($table, $column)->where(function ($query) use ($withSoftDeletes) {
            if ($withSoftDeletes) {
                $query->whereNull('deleted_at');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(ResponseUtil::makeError('Ha ocurrido un error de validación.', $errors), 400)
        );
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            response()->json(ResponseUtil::makeError('Este usuario no tiene permiso para realizar esta acción.'), 400)
        );
    }
}
