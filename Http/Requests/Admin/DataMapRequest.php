<?php

namespace Goodcatch\Modules\Core\Http\Requests\Admin;

use Goodcatch\Modules\Core\Http\Requests\BaseRequest as FormRequest;
use Goodcatch\Modules\Core\Model\Admin\DataMap;
use Illuminate\Validation\Rule;

class DataMapRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules ()
    {
        $status_in = [
            DataMap::STATUS_DISABLE,
            DataMap::STATUS_ENABLE,
        ];
        return [
            'data_route_id' => ['required', 'exists:core_data_routes,id'],
            'left' => ['required', 'max:50'],
            'left_table' => ['required', 'max:100'],
            'left_tpl' => ['required', 'max:500'],
            'right' => ['required', 'max:50'],
            'right_table' => ['required', 'max:100'],
            'right_tpl' => ['required', 'max:500'],
            'relationship' => 'required|max:50',
            'description' => 'max:500',
            'name' => 'max:100',
            'table' => 'max:100',
            'through' => 'max:100',
            'first_key' => 'max:100',
            'second_key' => 'max:100',
            'foreign_key' => 'max:100',
            'owner_key' => 'max:100',
            'local_key' => 'max:100',
            'second_local_key' => 'max:100',
            'foreign_pivot_key' => 'max:100',
            'related_pivot_key' => 'max:100',
            'parent_key' => 'max:100',
            'related_key' => 'max:100',
            'relation' => 'max:100',
            'status' => [
                'required',
                Rule::in ($status_in),
            ],
        ];
    }
}
