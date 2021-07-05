<?php

namespace Goodcatch\Modules\Core\Http\Requests\Admin;

use Goodcatch\Modules\Core\Http\Requests\BaseRequest as FormRequest;
use Goodcatch\Modules\Core\Model\Admin\DataRoute;

class DataRouteRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules ()
    {
        return [
            'name' => ['required', 'max:50', $this->uniqueOrExists (DataRoute::class, 'name') . ':core_dataroutes'],
            'alias' => 'max:50',
            'short' => 'max:20',
            'description' => 'max:255',
            'from' => 'max:50',
            'table_from' => 'max:100',
            'to' => 'max:50',
            'table_to' => 'max:100',
            'output' => 'max:100',
            'connection_id' => 'exists:core_connections,id'
        ];
    }
}
