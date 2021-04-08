<?php

namespace Goodcatch\Modules\Core\Http\Requests\Admin;

use Goodcatch\Modules\Core\Http\Requests\BaseRequest as FormRequest;

class DepartmentRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules ()
    {
        switch ($this->method ()) {
            case 'POST':
                return [
                    'name' => 'required|max:50',
                    'pid' => 'required|integer',
                    'order' => 'integer'
                ];
                break;
        }
        return [];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages ()
    {
        return [
            'name.required' => '名称不能为空',
            'name.max' => '名称长度不能大于50',
        ];
    }
}
