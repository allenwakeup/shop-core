<?php

namespace Goodcatch\Modules\Core\Http\Requests\Admin;

use Goodcatch\Modules\Core\Http\Requests\BaseRequest as FormRequest;
use Goodcatch\Modules\Core\Model\Admin\Attachment;

class AttachmentRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $status_in = [
            Attachment::STATUS_DISABLE,
            Attachment::STATUS_ENABLE,
        ];
        switch ($this->method()) {
            case 'POST':
                return [
                    'attachable' => 'required|string|in:' . implode(',', \collect(Attachment::ATTACHABLES_MAPPING)->keys()->all()),
                    'id' => 'required|numeric'
                ];
                break;
            case 'DELETE':
                return [
                    'attachable' => 'required|string|in:' . implode(',', \collect(Attachment::ATTACHABLES_MAPPING)->keys()->all()),
                    'attachable_id' => 'required|numeric',
                    'id' => 'required|numeric'
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
    public function messages()
    {
        return [
            'name.required' => '名称不能为空',
            'name.max' => '名称长度不能大于50',
        ];
    }
}
