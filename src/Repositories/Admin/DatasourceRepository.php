<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\Datasource;

class DatasourceRepository extends BaseRepository
{

    public static function list ($perPage, $condition = [], $keyword = null)
    {
        $data = Datasource::query ()
            ->where (function ($query) use ($condition, $keyword) {
                self::buildQuery ($query, $condition);
                if (! empty ($keyword))
                {
                    self::buildSelect ($query, $condition, $keyword);
                }
            })
            ->orderBy ('id', 'desc')
            ->paginate ($perPage);
        $data->transform (function ($item) {
            $item->editUrl = route ('admin::' . module_route_prefix ('.') . 'core.datasource.edit', ['id' => $item->id]);
            $item->deleteUrl = route ('admin::' . module_route_prefix ('.') . 'core.datasource.delete', ['id' => $item->id]);
            return $item;
        });

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data->total (),
            'data' => $data->items (),
        ];
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
