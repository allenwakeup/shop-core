<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\Datasource;

class DatasourceRepository extends BaseRepository
{

    public static function list($perPage, $condition = [], $keyword = null)
    {
        return Datasource::query()
            ->where(function ($query) use ($condition, $keyword) {
                self::buildQuery($query, $condition);
                if(!empty($keyword)){
                    $query->orWhere('code', 'like', "%$keyword%")
                        ->orWhere('name', 'like', "%$keyword%")
                        ->orWhere('description', 'like', "%$keyword%");
                }
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public static function add ($data)
    {
        return Datasource::query ()->create ($data);
    }

    public static function update ($id, $data)
    {
        return Datasource::find ($id)->update ($data);
    }

    public static function find ($id)
    {
        return Datasource::query ()->find ($id);
    }

    public static function delete ($id)
    {
        return Datasource::destroy ($id);
    }
}
