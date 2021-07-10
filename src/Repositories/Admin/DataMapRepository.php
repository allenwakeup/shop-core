<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\DataMap;
use Goodcatch\Modules\Core\Model\Admin\Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ReflectionClass;

class DataMapRepository extends BaseRepository
{

    public static $REFLECT;

    public static $REFLECT_METHOD_ARGS = [];

    public static function list ($perPage, $condition = [], $keyword = null)
    {
        $data = DataMap::query ()
            ->with ('dataRoute')
            ->where (function ($query) use ($condition, $keyword) {
                self::buildQuery ($query, $condition);
                if (! empty ($keyword))
                {
                    $query->orWhere('name', 'like', "%$keyword%")
                        ->orWhere('table', 'like', "%$keyword%")
                        ->orWhere('description', 'like', "%$keyword%")
                        ->orWhere('left', 'like', "%$keyword%")
                        ->orWhere('left_table', 'like', "%$keyword%")
                        ->orWhere('right', 'like', "%$keyword%")
                        ->orWhere('right_table', 'like', "%$keyword%");
                }
            })
            ->orderBy ('id', 'desc')
            ->paginate ($perPage);
        $data->transform (function ($item) {
            if (Arr::has (DataMap::RELATIONSHIPS, $item->relationship))
            {
                $item->relationshipText = Arr::get (DataMap::RELATIONSHIPS, $item->relationship, '--');
            }
            return $item;
        });

        return $data;
    }



    public static function add ($data)
    {
        return DataMap::query ()->create ($data);
    }

    public static function update ($id, $data)
    {
        $data_map = DataMap::find ($id);
        if (! empty ($data_map->output))
        {
            unset ($data ['output']);
        }
        unset ($data ['left_table']);
        unset ($data ['right_table']);

        return $data_map->update ($data);
    }

    public static function find ($id)
    {
        return DataMap::query ()->find ($id);
    }

    public static function delete ($id)
    {
        return DataMap::destroy ($id);
    }

    public static function allEnabled() {
        return DataMap::ofEnabled ()->get ();
    }

    public static function loadEnabledFromCache(){
        return Cache::rememberForever (config('modules.cache.key') . '.core.data_maps.enabled', function () {
            return \collect(self::allEnabled()->toArray());
        });
    }

    public static function relationships ()
    {

        $value = self::allEnabled ();

        if (! isset ($value) || $value->isEmpty ()) {
            return [];
        }

        return $value->reduce (function ($arr, $item) {

            $item_data = [];

            foreach ($item->toArray () as $k => $v)
            {
                $item_data [Str::camel($k)] = $v;
            }

            if (! Arr::has ($arr, Arr::get($item, 'left_table')))
            {
                $arr [Arr::get($item, 'left_table')] = [];
            }

            $args = \collect (self::getEloquentRelationArgs (Arr::get($item, 'relationship')))->reduce (function ($arr, $arg) use ($item, &$item_data) {

                if (! Arr::has ($item_data, $arg->getName ()))
                {
                    $update_arg_value = null;
                    switch ($arg->getName ())
                    {
                        case 'related':
                            $update_arg_value = Arr::get($item, 'right_table');
                            break;
                    }
                    if (! empty ($update_arg_value))
                    {
                        Arr::set ($item_data, $arg->getName (), $update_arg_value);
                    }
                }

                if (Arr::has ($item_data, $arg->getName ()))
                {
                    $arg_value = Arr::get ($item_data, $arg->getName (), null);
                    array_push ($arr, $arg_value);
                }

                return $arr;
            }, []);

            $arr [Arr::get($item, 'left_table')] ['_map_' . Arr::get($item, 'right_table')] = [
                'relationship' => Arr::get($item, 'relationship'),
                'args' => $args,
                'payload' => $item_data
            ];

            return $arr;
        }, []);

    }

    public static function loadFromCache(){
        return Cache::rememberForever (config('modules.cache.key') . '.core.data_maps', function () {
            return self::relationships();
        });
    }

    public static function assignment ($perPage, DataMap $dataMap, $table_left, $keyword = null){
        $data = [];
        if($table_left === $dataMap->left_table){
            $data = (new Eloquent)->setDataMapTable ($dataMap->left_table)
                ->newModelQuery ()
                ->where (function ($query) use ($dataMap, $keyword) {

                    foreach (explode ('+', $dataMap->left_tpl) as $tpl)
                    {
                        $query->orWhere (Arr::first (explode ('::', $tpl, 2)), 'like', '%' . $keyword . '%');
                    }
                })
                ->orderBy ('id', 'asc')
                ->paginate ($perPage);

            $data->transform (function ($item) use ($dataMap) {

                $transform = '';
                foreach (explode ('+', $dataMap->left_tpl) as $k => $tpl)
                {
                    [$name, $rules] = array_merge (explode ('::', trim ($tpl), 2), [null]);
                    $mid_transform = $item->{$name};
                    if (! empty ($rules)) {
                        foreach (explode('|', $rules) as $rule) {
                            $mid_transform = transform_in_rules($rule, $mid_transform);
                        }
                    }
                    $transform .= $mid_transform;
                }

                return [
                    'title' => $transform,
                    'value' => $item->{$dataMap->parent_key}
                ];
            });

        }

        return $data;
    }

    public static function assignmentSource (DataMap $dataMap, $left_id)
    {
        // 根据数据映射定义，获取映射左边模型
        $data = [];
        $pivot_Eloquent = (new Eloquent)
            ->setDataMapTable ($dataMap->left_table)
            ->newModelQuery ()
            ->firstWhere ($dataMap->parent_key, $left_id);
        if (isset ($pivot_Eloquent))
        {
            $data = (new Eloquent)->setDataMapTable ($dataMap->right_table)->newModelQuery ()->get ();

            $data->transform (function ($item) use ($dataMap) {

                $transform = '';
                foreach (explode ('+', $dataMap->right_tpl) as $k => $tpl)
                {
                    [$name, $rules] = array_merge (explode ('::', trim ($tpl), 2), [null]);
                    $mid_transform = $item->{$name};
                    if (! empty ($rules)) {
                        foreach (explode('|', $rules) as $rule) {
                            $mid_transform = transform_in_rules($rule, $mid_transform);
                        }
                    }
                    $transform .= $mid_transform;
                }

                return [
                    'title' => $transform,
                    'key' => $item->{$dataMap->related_key}
                ];
            });
        }
        return $data;
    }

    public static function assignmentTarget (DataMap $dataMap, $left_id)
    {
        // 根据数据映射定义，获取映射左边模型
        $data = [];
        $pivot_Eloquent = (new Eloquent)
            ->setDataMapTable ($dataMap->left_table)
            ->newModelQuery ()
            ->firstWhere ($dataMap->parent_key, $left_id);
        if (isset ($pivot_Eloquent))
        {
            // $dataMapTable = '_map_' . $dataMapTable;

            // 动态设置左边模型的关联关系（此处当前仅当支持多态多对多关联关系）
            $pivot_Eloquent = $pivot_Eloquent->setDataMapTable ($dataMap->left_table)->getDataMapping ($dataMap->right_table);

            // 如果存在关联关系
            if (isset ($pivot_Eloquent))
            {
                $data = $pivot_Eloquent->get()->pluck ($dataMap->related_key);
            }
        }
        return $data;
    }
//
//    public static function select ($perPage, DataMap $dataMap, $left_id, $keyword = null)
//    {
//        $dataMapTable = $dataMap->right_table;
//        $dataMapKey = $dataMap->related_key;
//        $is_select_left = strcmp ($left_id, $dataMap->left_table) === 0;
//        if ($is_select_left)
//        {
//            $dataMapTable = $dataMap->left_table;
//            $dataMapKey = $dataMap->parent_key;
//        }
//
//        $data = (new Eloquent);
//        if ($is_select_left)
//        {
//            $data = $data->setDataMapTable ($dataMapTable)
//                ->newModelQuery ()
//                ->where (function ($query) use ($dataMap, $keyword) {
//
//                    foreach (explode ('+', $dataMap->left_tpl) as $tpl)
//                    {
//                        $query->orWhere (Arr::first (explode ('::', $tpl, 2)), 'like', '%' . $keyword . '%');
//
//                    }
//                })
//                ->orderBy ('id', 'desc')
//                ->paginate ($perPage);
//        } else {
//
//            $data = $data->setDataMapTable ($dataMap->left_table)->newModelQuery ()->firstWhere ($dataMap->parent_key, $left_id);
//            if (isset ($data))
//            {
//                // $dataMapTable = '_map_' . $dataMapTable;
//
//                $data = $data->setDataMapTable ($dataMap->left_table)->getDataMapping ($dataMapTable);
//
//                if (isset ($data))
//                {
//                    $data = $data->get ();
//
//                    if (isset ($data) && $data->count () > 0)
//                    {
//                        $right = $data->pluck ($dataMap->related_key);
//                    }
//                }
//
//                $data = (new Eloquent)->setDataMapTable ($dataMap->right_table)->newModelQuery ()->get ();
//
//            }
//
//        }
//
//        if (isset ($data))
//        {
//            $data->transform (function ($item) use ($dataMap, $is_select_left, $dataMapKey) {
//
//                $data_map_tpl = $dataMap->right_tpl;
//                if ($is_select_left)
//                {
//                    $data_map_tpl = $dataMap->left_tpl;
//                }
//
//                if (! empty ($data_map_tpl))
//                {
//                    $transform = '';
//                    foreach (explode ('+', $data_map_tpl) as $k => $tpl)
//                    {
//                        [$name, $rules] = array_merge (explode ('::', trim ($tpl), 2), [null]);
//                        $mid_transform = $item->{$name};
//                        if (! empty ($rules)) {
//                            foreach (explode('|', $rules) as $rule) {
//                                $mid_transform = transform_in_rules($rule, $mid_transform);
//                            }
//                        }
//                        $transform .= $mid_transform;
//                    }
//                    $item->title = $transform;
//                }
//                $item->value = $item->{$dataMapKey};
//
//                return $item;
//            });
//        }
//
//
//        return [
//            'data' => isset ($data) ? ($data instanceof Collection ? $data->all () : $data->items ()) : [],
//            'right' => isset ($right) ? $right : []
//        ];
//    }

    public static function addAssignments (DataMap $dataMap, $attach, $left_id)
    {
        $attached = [];
        if (isset ($dataMap) && $dataMap->status === DataMap::STATUS_ENABLE)
        {
            $attached = (new Eloquent)->setDataMapTable ($dataMap->left_table)
                ->firstWhere ($dataMap->parent_key, $left_id)
                ->setDataMapTable ($dataMap->left_table)
                ->getDataMapping ($dataMap->right_table)
                ->attach (\collect ($attach)->reduce (function ($arr, $right_id) use ($dataMap) {
                    $arr [$right_id] = [config ('core.data_mapping.' . $dataMap->relationship . '.right', 'right') . '_type' => $dataMap->right_table];
                    return $arr;
                }, []));
        }
        return $attached;
    }
    public static function deleteAssignments (DataMap $dataMap, $detach, $left_id)
    {
        $detached = [];
        if (isset ($dataMap) && $dataMap->status === DataMap::STATUS_ENABLE)
        {
            $detached = (new Eloquent)->setDataMapTable ($dataMap->left_table)
                ->firstWhere ($dataMap->parent_key, $left_id)
                ->setDataMapTable ($dataMap->left_table)
                ->getDataMapping ($dataMap->right_table)
                ->detach ($detach);
        }
        return $detached;
    }


    /**
     * get Eloquent method args
     *
     * @param $method
     * @return array|\ReflectionParameter[]
     * @throws \ReflectionException
     */
    private static function getEloquentRelationArgs ($method)
    {
        if (! self::$REFLECT)
        {
            self::$REFLECT = new ReflectionClass (Eloquent::class);
        }

        if (! Arr::has (self::$REFLECT_METHOD_ARGS, $method))
        {
            if (self::$REFLECT->hasMethod ($method))
            {
                Arr::set (self::$REFLECT_METHOD_ARGS, $method, self::$REFLECT->getMethod ($method)->getParameters ());
            }
        }

        return Arr::get (self::$REFLECT_METHOD_ARGS, $method, []);
    }

}
