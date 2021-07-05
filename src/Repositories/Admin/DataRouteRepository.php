<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\DataRoute;
use Goodcatch\Modules\Core\Model\Admin\Eloquent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DataRouteRepository extends BaseRepository
{

    public static function list ($perPage, $condition = [], $keyword = null)
    {

        $data = DataRoute::query ()
            ->with (['connection.datasource'])
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

            return $item;
        });

        return $data;
    }

    public static function from (DataRoute $data_route, $perPage, $page, $keyword)
    {
        if (isset ($data_route))
        {
            $data_maps = $data_route->data_maps;

            $data_from = $data_maps
                ->filter (function ($item) use ($data_route) {
                    return $item->left_table === $data_route->table_from;
                })
                ->groupBy ('table')->reduce (function ($arr, $items) use ($data_route, $keyword) {

                    $data_map = $items->first (function ($value, $key) use ($data_route) {
                        return $value->left_table === $data_route->table_from;
                    });

                    if (isset ($data_map))
                    {
                        $data_table_from = (new Eloquent ())->setTable ($data_route->table_from)
                            ->newModelQuery ()
                            ->select ("{$data_route->table_from}.*")
                            ->join (
                                $data_map->table,
                                "{$data_route->table_from}.{$data_map->parent_key}",
                                '=',
                                $data_map->table . '.' . config("core.data_mapping.{$data_map->relationship}.left_id", 'left_id')
                            )->where (
                                $data_map->table
                                . '.' . config("core.data_mapping.{$data_map->relationship}.left", 'left'). '_type',
                                $data_route->table_from
                            )->orWhere (function ($query) use ($items) {

                                $items->each (function ($data_map) use ($query) {
                                    $query->orWhere (
                                        $data_map->table
                                        . '.' . config("core.data_mapping.{$data_map->relationship}.right", 'right'). '_type',
                                        'like',
                                        $data_map->right_table
                                    );
                                });


                            })->get ();

                        $arr = $arr->merge ($data_table_from->all ())
                            ->unique (function ($unique) use ($data_map) {
                                return $unique [$data_map->parent_key];
                            })
                            ->transform (function ($item) use ($data_map) {

                                $data_map_tpl = $data_map->left_tpl;

                                if (! empty ($data_map_tpl))
                                {
                                    $transform = '';
                                    foreach (explode ('+', $data_map_tpl) as $k => $tpl)
                                    {
                                        [$name, $rules] = array_merge (explode ('::', trim ($tpl), 2), [null]);
                                        $mid_transform = $item->{$name};
                                        if (! empty ($rules))
                                        {
                                            foreach (explode ('|', $rules) as $rule)
                                            {
                                                $mid_transform = transform_in_rules ($rule, $mid_transform);
                                            }
                                        }
                                        $transform .= $mid_transform;

                                    }
                                    $item->name = $transform;
                                }
                                $item->id = $item->{$data_map->parent_key};

                                return $item;
                            })
                            ->filter (function ($filter) use ($keyword) {
                                return empty ($keyword) || Str::contains ($filter->name, $keyword);
                            })
                            ->sortBy ('name');
                    }

                    return $arr;
                }, \collect ([]));

            return $data_from->slice ($page * $perPage, $perPage)->values ()->all ();

        }

        return [];
    }

    public static function to (DataRoute $data_route, $left_id)
    {
        if (isset ($data_route))
        {
            $data_maps = $data_route->data_maps;

            $groups = [];
            $data_to = $data_maps
                ->filter (function ($item) use ($data_route) {
                    return $item->right_table === $data_route->table_to;
                })
                ->groupBy ('table')->reduce (function ($arr, $items) use ($data_route, $left_id, &$groups) {

                    $data_map = $items->first (function ($value, $key) use ($data_route) {
                        return $value->left_table === $data_route->table_from;
                    });

                    if (isset ($data_map))
                    {
                        $data_map_conf = config("core.data_mapping.{$data_map->relationship}", []);
                        $data_map_conf_left = Arr::get ($data_map_conf, 'left', 'left') . '_type';
                        $data_map_conf_left_id = Arr::get ($data_map_conf, 'left_id', 'left_id');
                        $data_map_conf_right = Arr::get ($data_map_conf, 'right', 'right') . '_type';
                        $data_map_conf_right_id = Arr::get ($data_map_conf, 'right_id', 'right_id');

                        $data_table_from = (new Eloquent ())->setTable ($data_map->table)
                            ->newModelQuery ()
                            ->select ([
                                $data_map_conf_right,
                                $data_map_conf_right_id
                            ])
                            ->where (
                                $data_map_conf_left_id,
                                $left_id
                            )->where (
                                $data_map_conf_left,
                                $data_route->table_from
                            )->orWhere (function ($query) use ($items, $data_route, $data_map_conf_right) {
                                $items
                                    ->filter (function ($item) use ($data_route) {
                                        return $item->right_table !== $data_route->table_to;
                                    })
                                    ->each (function ($data_map) use ($query, $data_map_conf_right) {
                                        $query->orWhere (
                                            $data_map_conf_right,
                                            $data_map->right_table
                                        );
                                    });
                            })->get ()->filter (function ($filter_from) use ($data_map_conf_right, $data_route) {
                                return $filter_from->{$data_map_conf_right} !== $data_route->table_to;
                            });

                        $data_map = $items->first (function ($value, $key) use ($data_route) {
                            return $value->right_table === $data_route->table_to;
                        });

                        if (isset ($data_map))
                        {
                            if (! empty ($data_map->right_tpl)) {
                                foreach (explode('+', $data_map->right_tpl) as $k => $tpl) {
                                    $groups [] = Arr::first (array_merge(explode('::', trim($tpl), 2), [null]));
                                }
                            }

                            $data_table_to = (new Eloquent ())->setTable ($data_route->table_to)
                                ->newModelQuery ()
                                ->select ("{$data_route->table_to}.*")
                                ->join ($data_map->table, function ($join) use ($data_route, $data_map, $data_map_conf_right, $data_map_conf_right_id) {
                                    $join->on (
                                        "{$data_route->table_to}.{$data_map->related_key}",
                                        "=",
                                        "{$data_map->table}.{$data_map_conf_right_id}"
                                    )->where ("{$data_map->table}.{$data_map_conf_right}", $data_route->table_to);
                                })->orWhere (function ($query) use ($data_table_from, $data_map, $left_id, $data_route, $data_map_conf_left, $data_map_conf_left_id, $data_map_conf_right, $data_map_conf_right_id) {
                                    $query->orWhere (function ($query) use ($data_route, $data_map, $data_map_conf_left, $data_map_conf_left_id, $left_id){
                                        $query
                                            ->where ("{$data_map->table}.{$data_map_conf_left}", $data_route->table_from)
                                            ->where ("{$data_map->table}.{$data_map_conf_left_id}", $left_id);
                                    });
                                    $data_table_from
                                        ->each (function ($data_table_from_item) use ($query, $data_map,$data_map_conf_left, $data_map_conf_left_id, $data_map_conf_right, $data_map_conf_right_id) {
                                            $query->orWhere (function ($orWhere) use ($data_map, $data_map_conf_left, $data_map_conf_left_id, $data_table_from_item, $data_map_conf_right, $data_map_conf_right_id){
                                                $orWhere
                                                    ->where ("{$data_map->table}.{$data_map_conf_left}", $data_table_from_item->{$data_map_conf_right})
                                                    ->where ("{$data_map->table}.{$data_map_conf_left_id}", $data_table_from_item->{$data_map_conf_right_id});
                                            });
                                        });
                                })->get ();

                            $arr = $arr->merge ($data_table_to->all ())
                                ->unique (function ($unique) use ($data_map) {
                                    return $unique->{$data_map->related_key};
                                })
                                ->transform (function ($item) use ($data_map) {

                                    $data_map_tpl = $data_map->right_tpl;

                                    if (! empty ($data_map_tpl))
                                    {
                                        $transform = '';
                                        foreach (explode ('+', $data_map_tpl) as $k => $tpl)
                                        {
                                            [$name, $rules] = array_merge (explode ('::', trim ($tpl), 2), [null]);
                                            $mid_transform = $item->{$name};
                                            if (! empty ($rules))
                                            {
                                                foreach (explode ('|', $rules) as $rule)
                                                {
                                                    $mid_transform = transform_in_rules ($rule, $mid_transform);
                                                }
                                            }
                                            $transform .= $mid_transform;
                                        }
                                        $item->name = $transform;
                                    }
                                    $item->id = $item->{$data_map->related_key};

                                    return $item;
                                })
                                ->sortBy ('name');
                        }

                    }
                    return $arr;
                }, \collect ([]));

            // 转换成树形数据
            $data_to = collection2TreeData ($data_to, $groups, 'title', 'children', 'id', 'name');

            return $data_to->values ()->all ();

        }

        return [];
    }

    public static function add ($data)
    {
        return DataRoute::query ()->create ($data);
    }

    public static function update ($id, $data)
    {
        unset ($data ['table_from']);
        unset ($data ['table_to']);
        unset ($data ['table_output']);

        return DataRoute::find ($id)->update ($data);
    }

    public static function find ($id)
    {
        return DataRoute::query ()->find ($id);
    }

    public static function delete ($id)
    {
        return DataRoute::destroy ($id);
    }
}
