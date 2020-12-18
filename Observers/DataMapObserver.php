<?php


namespace Goodcatch\Modules\Core\Observers;

use Goodcatch\Modules\Core\Model\Admin\DataMap;
use Illuminate\Support\Arr;


/**
 * 更新缓存中的菜单
 *
 * Class DataMapObserver
 * @package Goodcatch\Modules\Core\Observers
 */
class DataMapObserver
{

    // creating, created, updating, updated, saving
    // saved, deleting, deleted, restoring, restored
    public function created (DataMap $item)
    {
        // load menu from other module, e.g Lightcms
        module_tap ('LightcmsMenuQuery', function ($query) use ($item) {

            // get menu query builder from application abstract

            $menu = $query
                ->where ('route', 'admin::' . module_route_prefix ('.') . '.core.dataMap.assignment')
                ->where ('route_params', '')
                ->first ()
            ;

            if (isset ($menu))
            {

                // load root menu from other module
                module_tap ('LightcmsRootMenu', function ($root_menu) use ($item, $menu) {

                    $menu_route = $menu->route . '.' . ($item->left_table . '_' . $item->right_table);

                    $data = [
                        'name' => $item->title,
                        'pid' => empty ($root_menu) ? $menu->id : Arr::get ($root_menu, 'id', $menu->id),
                        'status' => 1,
                        'order' => $item->id,
                        'route' => $menu_route,
                        'url' => route ('admin::' . module_route_prefix ('.') . 'core.dataMap.assignment', ['id' => $item->id], false) . '?pop=0',
                        'group' => (isset ($item->data_route) && ! empty ($item->data_route->alias)) ? $item->data_route->alias : $menu->group,
                        'guard_name' => $menu->guard_name,
                        'is_lock_name' => 0
                    ];

                    module_tap ('LightcmsMenuUpdateOrCreate', function ($new_menu) use ($data) {
                        if (isset ($new_menu))
                        {
                            $this->dispatchChanges ();
                        }
                    }, [
                        'attributes'    => ['route' => $menu_route],
                        'values'        => $data
                    ]);

                }, ['route' => $menu->route]);

            }
        });

    }

    public function deleting (DataMap $item)
    {
        $menu_route = 'admin::' . module_route_prefix ('.') . 'core.dataMap.assignment.' . ($item->left_table . '_' . $item->right_table);

        module_tap ('LightcmsMenuQuery', function ($query) use ($menu_route) {

            $query->where ('route', $menu_route)->delete ();

            $this->dispatchChanges ();

        });

    }

    private function dispatchChanges () {
        module_tap ('LightcmsFlushMenuJob', function ($job) {
            dispatch ($job);
        });
    }

}