<?php

namespace Goodcatch\Modules\Core\Http\Resources\Admin\DatabaseResource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DatabaseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data'=>$this->collection->toArray(),
            'total'=>$this->collection->count(), // 数据总数
        ];
    }
}
