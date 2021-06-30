<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\Connection;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ConnectionRepository extends BaseRepository
{


    public static function list ($perPage, $condition = [], $keyword = null)
    {
        $data = Connection::query ()
            ->with ('datasource')
            ->where (function ($query) use ($condition, $keyword) {
                self::buildQuery ($query, $condition);
            })
            ->orderBy ('id', 'desc')
            ->paginate ($perPage);
        $data->transform (function ($item) {
            $item->typeText = Arr::get (Connection::TYPE, $item->type, '--');
            $item->password = empty($item->password)
                ? '****'
                : (substr($item->password, 0, 1) . '****');
            return $item;
        });

        return $data;
    }

    public static function add ($data)
    {
        if(empty($data['password'])){
            unset($data['password']);
        }
        self::transform (self::TRANSFORM_TYPE_JSON, $data, 'options');
        return Connection::query ()->create ($data);
    }

    public static function update ($id, $data)
    {
        if(empty($data['password'])){
            unset($data['password']);
        }
        self::transform (self::TRANSFORM_TYPE_JSON, $data, 'options');
        return Connection::find ($id)->update ($data);
    }

    public static function find ($id)
    {
        $data = Connection::query ()->find ($id);
        if(!empty($data)){
            $data = $data->toArray();
            unset($data['password']);
        }
        return $data;
    }

    public static function delete ($id)
    {
        return Connection::destroy ($id);
    }

    public static function all ()
    {
        return Cache::rememberForever (config('modules.cache.key') . '.core.connections', function () {

            $connection = new Connection;
            if(!Schema::hasTable($connection->getTable())){
                return [];
            }

            $value = $connection::ofEnabled ()->get ();
            if ($value->isEmpty ()) {
                return [];
            }

            return $value->mapWithKeys (function ($item) {

                $item_data = $item->toArray ();
                $connection = [];
                foreach (explode (',', $item->datasource->requires . ',' . $item->datasource->options) as $key)
                {
                    $key = explode (':', trim($key), 2) [0];

                    if (! empty ($key))
                    {
                        $connection [$key] = Arr::get ($item_data, $key, '');
                    }
                }

                Arr::set ($connection, 'type', $item->type);
                Arr::set ($connection, 'name', $item->name);

                if (Arr::has ($connection, 'options') && empty (Arr::get ($connection, 'options', '')))
                {
                    unset ($connection ['options']);
                }

                if (! empty ($item->options))
                {
                    Arr::set ($connection, 'options', $item->options);
                }

                return [$item->type . '_' . $item->name => $connection];
            })->all ();
        });
    }
}
