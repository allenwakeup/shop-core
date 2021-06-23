<?php

namespace Goodcatch\Modules\Core\Http\Requests\Admin;

use Goodcatch\Modules\Core\Http\Requests\BaseRequest as FormRequest;


class AreaRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|max:20|exists:county,county_id',
            'name' => 'required|max:50',
            'short' => 'max:50',
            'alias' => 'max:50',
            'display' => 'max:50',
            'description' => 'max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required' => '请选择省市县',
            'code.exists' => '省市县编码错误',
            'code.max' => '省市县编码长度不能大于20'
        ];
    }
}
