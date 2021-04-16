<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\LightCMS\Admin;

use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Events\DataMapUpdated;
use Goodcatch\Modules\Core\Http\Requests\Admin\DataMapRequest;
use Goodcatch\Modules\Core\Jobs\SyncDataMappingData;
use Goodcatch\Modules\Core\Model\Admin\Eloquent;
use Goodcatch\Modules\Core\Repositories\Admin\DataMapRepository;
use Goodcatch\Modules\Core\Model\Admin\DataMap;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DataMapController extends Controller
{
    protected $formNames = ['data_route_id', 'left', 'left_table', 'left_tpl', 'right',
        'right_table', 'right_tpl', 'relationship', 'description', 'name', 'table',
        'through', 'first_key', 'second_key', 'foreign_key', 'owner_key',
        'local_key', 'second_local_key', 'foreign_pivot_key', 'related_pivot_key',
        'parent_key', 'related_key', 'relation', 'status'
    ];

    public function __construct ()
    {
        parent::__construct ();

        $this->breadcrumb [] = ['title' => '数据映射列表', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.dataMap.index')];
    }

    /**
     * 数据源管理-数据映射
     *
     */
    public function index ()
    {
        $this->breadcrumb [] = ['title' => '数据映射列表', 'url' => ''];
        return view ('core::admin.dataMap.index', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据映射管理-数据映射列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function list (Request $request)
    {
        $perPage = (int) $request->get ('limit', 50);
        $condition = $request->only ($this->formNames);

        $data = DataMapRepository::list ($perPage, $condition, $request->keyword);

        return $data;
    }

    /**
     * 数据映射管理-新增数据映射
     *
     */
    public function create ()
    {
        $this->breadcrumb [] = ['title' => '新增数据映射', 'url' => ''];
        return view ('core::admin.dataMap.add', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据映射管理-数据映射详情
     *
     */
    public function detail ($id)
    {
        return view ('admin.detail', ['model' => DataMap::find ($id)]);
    }

    /**
     * 数据映射管理-保存数据映射
     *
     * @param DataMapRequest $request
     * @return array
     */
    public function save (DataMapRequest $request)
    {
        try {
            DataMapRepository::add ($request->only ($this->formNames));
            event (new DataMapUpdated ());
            return [
                'code' => 0,
                'msg' => '新增成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '新增失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据映射已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 数据映射管理-编辑数据映射
     *
     * @param int $id
     * @return View
     */
    public function edit ($id)
    {
        $this->breadcrumb [] = ['title' => '编辑数据映射', 'url' => ''];

        $model = DataMapRepository::find ($id);
        return view ('core::admin.dataMap.add', ['id' => $id, 'model' => $model, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据映射管理-更新数据映射
     *
     * @param DataMapRequest $request
     * @param int $id
     * @return array
     */
    public function update (DataMapRequest $request, $id)
    {
        $data = $request->only ($this->formNames);
        try {
            DataMapRepository::update ($id, $data);
            event (new DataMapUpdated ());
            return [
                'code' => 0,
                'msg' => '编辑成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '编辑失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据映射已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 数据映射管理-删除数据映射
     *
     * @param int $id
     * @return array
     */
    public function delete ($id)
    {
        try {
            DataMapRepository::delete ($id);
            event (new DataMapUpdated ());
            return [
                'code' => 0,
                'msg' => '删除成功',
                'redirect' => route ('admin::' . module_route_prefix ('.') . 'core.dataMap.index')
            ];
        } catch (\RuntimeException $e) {
            return [
                'code' => 1,
                'msg' => '删除失败：' . $e->getMessage (),
                'redirect' => false
            ];
        }
    }


    /**
     * 数据映射管理-映射数据
     *
     * @param int $id
     * @return array
     */
    public function assignment ($id)
    {
        $is_pop = (request ()->get ('pop') . '') === '1';

        $this->breadcrumb[] = ['title' => '映射数据', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.dataMap.index')];

        $dataMap = DataMap::find ($id);

        $view_model = [
            'is_pop' => $is_pop,
            'breadcrumb' => $this->breadcrumb,
            'action' => [
                'POST' => route ('admin::' . module_route_prefix ('.') . 'core.dataMap.assignment.save', ['id' => $id, 'left_id' => 'left_id']),
                'DELETE' => route ('admin::' . module_route_prefix ('.') . 'core.dataMap.assignment.delete', ['id' => $id, 'left_id' => 'left_id']),
            ],
        ];

        if (isset ($dataMap))
        {
            $this->breadcrumb[] = ['title' => $dataMap->left . '-' . $dataMap->right . '映射', 'url' => ''];
            $view_model ['breadcrumb'] = $this->breadcrumb;
            $view_model ['id'] = $id;
            $view_model ['mapping'] = $dataMap->left_table . '-' . $dataMap->right_table;
            $view_model ['model'] = $dataMap;
            $view_model ['select'] = route ('admin::' . module_route_prefix ('.') . 'core.dataMap.assignment.select', ['id' => $id, 'left_id' => $dataMap->left_table]);

        }
        return view ('core::admin.dataMap.assignment', $view_model);

    }

    /**
     * 数据映射管理-左表列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function selectAssignment ($id, $left_id, Request $request)
    {

        $perPage = (int) $request->get ('limit', 50);

        $dataMap = DataMap::find ($id);

        $data = [
            'code' => 0,
            'msg' => '',
            'count' => 0,
            'data' => [],
        ];

        if (isset ($dataMap))
        {
            $data = DataMapRepository::select ($perPage, $dataMap, $left_id, $request->keyword);
        }

        return $data;


    }

    /**
     * 数据映射管理-保存映射的数据
     *
     * @param $id
     * @return array
     */
    public function saveAssignment ($id, $left_id)
    {
        $ids = request ()->input ('id.*');
        if (isset ($ids) && count ($ids) > 0)
        {
            $dataMap = DataMap::find ($id);

            if (isset ($dataMap) && $dataMap->status === DataMap::STATUS_ENABLE)
            {
                try {
                    (new Eloquent)->setDataMapTable ($dataMap->left_table)
                        ->firstWhere ($dataMap->parent_key, $left_id)
                        ->setDataMapTable ($dataMap->left_table)
                        ->getDataMapping ($dataMap->right_table)
                        ->attach (\collect ($ids)->reduce (function ($arr, $right_id) use ($dataMap) {
                            $arr [$right_id] = [config ('core.data_mapping.' . $dataMap->relationship . '.right', 'right') . '_type' => $dataMap->right_table];
                            return $arr;
                        }, []));

                    dispatch (new SyncDataMappingData (array_merge ($dataMap->toArray (), [
                        'title' => $dataMap->title,
                        'left_id' => $left_id,
                        'data_route' => isset ($dataMap->data_route) ? $dataMap->data_route->toArray () : [],
                        'connection' => isset ($dataMap->data_route)
                            ? (
                                isset ($dataMap->data_route->connection)
                                    ? $dataMap->data_route->connection->name
                                    : ''
                            )
                            : ''
                    ])));

                    return [
                        'code' => 0,
                        'msg' => '保存成功',
                        'redirect' => route ('admin::' . module_route_prefix ('.') . 'core.dataMap.assignment', ['id' => $id])
                    ];
                } catch (\RuntimeException $e) {
                    return [
                        'code' => 1,
                        'msg' => '保存失败：' . $e->getMessage (),
                        'redirect' => false
                    ];
                }
            }
        }


    }

    /**
     * 数据映射管理-删除映射的数据
     *
     * @param $id
     * @return array
     */
    public function deleteAssignment ($id, $left_id)
    {
        $ids = request ()->input ('id.*');
        if (isset ($ids) && count ($ids) > 0)
        {
            $dataMap = DataMap::find ($id);
            if (isset ($dataMap) && $dataMap->status === DataMap::STATUS_ENABLE)
            {
                try {
                    (new Eloquent)->setDataMapTable ($dataMap->left_table)
                        ->firstWhere ($dataMap->parent_key, $left_id)
                        ->setDataMapTable ($dataMap->left_table)
                        ->getDataMapping ($dataMap->right_table)
                        ->detach ($ids);

                    dispatch (new SyncDataMappingData (array_merge ($dataMap->toArray (), [
                        'title' => $dataMap->title,
                        'left_id' => $left_id,
                        'detach' => true,
                        'data_route' => isset ($dataMap->data_route) ? $dataMap->data_route->toArray () : [],
                        'connection' => isset ($dataMap->data_route)
                            ? (isset ($dataMap->data_route->connection)
                                ? $dataMap->data_route->connection->name
                                : '')
                            : ''
                    ])));

                    return [
                        'code' => 0,
                        'msg' => '删除成功',
                        'redirect' => route('admin::' . module_route_prefix ('.') . 'core.dataMap.assignment', ['id' => $id])
                    ];
                } catch (\RuntimeException $e) {
                    return [
                        'code' => 1,
                        'msg' => '删除失败：' . $e->getMessage(),
                        'redirect' => false
                    ];
                }
            }
        }
    }

}
