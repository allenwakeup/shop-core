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
            ->with (['county.city.province'])
            ->where(function ($query) use ($condition) {
                self::buildQuery($query, $condition);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public static function select ($condition = [], $select = 'province', $keyword = null)
    {

        $select_class = [
            'province' => Province::class,
            'city' => City::class,
            'county' => County::class
        ][$select];

        if (isset ($select_class))
        {
            $data = $select_class::query ()
                ->select([
                    'name',
                    'name as label',
                    \DB::raw("'{$select}' as cascader"),
                    "{$select}_id as value",
                    "{$select}_id as code"
                ])
                ->where(function ($query) use ($condition, $keyword) {
                    self::buildQuery ($query, $condition);
                    if (! empty ($keyword))
                    {
                        $query->where('name', 'like', "%{$keyword}%");
                    }
                })
                ->orderBy ($select . '_id', 'asc')
                ->get()
                ->transform (function ($item) use ($select) {
                    if ($select !== 'county')
                    {
                        $item->isLeaf = false;
                    }
                    return $item;
                });
            return $data->all();
        }

        return [];
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
