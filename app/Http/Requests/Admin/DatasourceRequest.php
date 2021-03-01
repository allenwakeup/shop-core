<?php

namespace Goodcatch\Modules\Core\Http\Requests\Admin;

use Goodcatch\Modules\Core\Http\Requests\BaseRequest as FormRequest;
use Goodcatch\Modules\Core\Model\Admin\Datasource;
use Illuminate\Validation\Rule;

class DatasourceRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules ()
    {
        $status_in = [
            Datasource::STATUS_DISABLE,
            Datasource::STATUS_ENABLE,
        ];
        return [
            'code' => ['required', 'max:50', $this->uniqueOrExists (Datasource::class, 'code') . ':core_datasources'],
            'name' => 'required|max:50',
            'description' => 'max:255',
            'requires' => 'required|max:2000',
            'options' => 'max:2000',
            'order' => 'required|numeric',
            'status' => [
                'required',
                Rule::in ($status_in),
            ],
        ];
    }
}
