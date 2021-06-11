<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\Area;
use Goodcatch\Modules\Core\Model\Admin\City;
use Goodcatch\Modules\Core\Model\Admin\County;
use Goodcatch\Modules\Core\Model\Admin\Province;

class AreaRepository extends BaseRepository
{

    public static function list($perPage, $condition = [])
    {
        return Area::query()
            ->with (['region.city.province'])
            ->where(function ($query) use ($condition) {
                self::buildQuery($query, $condition);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public static function select ($perPage, $condition = [], $select = 'province', $keyword = null)
    {

        $select_class = [
            'province' => Province::class,
            'city' => City::class,
            'county' => County::class
        ][$select];

        if (isset ($select_class))
        {
            $data = (app ()->make ($select_class))::query ()
                ->where(function ($query) use ($condition, $keyword) {
                    self::buildQuery ($query, $condition);
                    if (! empty ($keyword))
                    {
                        self::buildSelect ($query, $condition, $keyword);
                    }
                })
                ->orderBy ($select . '_id', 'asc')
                ->paginate ($perPage);

            $data->transform (function ($item) use ($select) {
                $item->value = $item [$select . '_id'];
                if ($select !== 'county')
                {
                    $item->children = [];
                }
                $item->cascader = $select;
                return $item;
            });
        }

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data->total (),
            'data' => $data->items (),
        ];
    }

    public static function add($data)
    {
        return Area::query()->create($data);
    }

    public static function update($id, $data)
    {
        return Area::query()->where('id', $id)->update($data);
    }

    public static function find($id)
    {
        return Area::query()->find($id);
    }

    public static function delete($id)
    {
        return Area::destroy($id);
    }
}
