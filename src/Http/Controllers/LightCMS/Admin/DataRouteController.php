<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\LightCMS\Admin;

use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Http\Requests\Admin\DataRouteRequest;
use Goodcatch\Modules\Core\Repositories\Admin\DataRouteRepository;
use Goodcatch\Modules\Core\Model\Admin\DataRoute;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DataRouteController extends Controller
{
    protected $formNames = ['name', 'alias', 'short', 'from', 'table_from', 'to', 'table_to', 'output', 'connection_id', 'description'];

    public function __construct ()
    {
        parent::__construct ();

        $this->breadcrumb [] = ['title' => '数据路径列表', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.index')];
    }

    /**
     * 数据源管理-数据路径
     *
     */
    public function index ()
    {
        $this->breadcrumb [] = ['title' => '数据路径列表', 'url' => ''];
        return view ('core::admin.dataRoute.index', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据路径管理-数据路径列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function list (Request $request)
    {
        $perPage = (int) $request->get ('limit', 50);
        $condition = $request->only ($this->formNames);

        $data = DataRouteRepository::list ($perPage, $condition, $request->keyword);

        return $data;
    }

    /**
     * 数据路径管理-新增数据路径
     *
     */
    public function create ()
    {
        $this->breadcrumb [] = ['title' => '新增数据路径', 'url' => ''];
        return view ('core::admin.dataRoute.add', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据路径管理-数据路径详情
     *
     */
    public function detail ($id, Request $request)
    {

        $data_route = DataRoute::find ($id);

        if (! empty ($request->type))
        {
            $perPage = (int) $request->get ('limit', 50);
            $page = (int) $request->get ('page', 1);

            switch ($request->type)
            {
                case 'from':
                    return DataRouteRepository::from ($data_route, $perPage, $page - 1, $request->keyword);
                    break;
                case 'to':
                    return DataRouteRepository::to ($data_route, $request->left_id);
                    break;
            }


        }

        return view ('core::admin.dataRoute.detail', [
            'model' => $data_route,
            'left_data_map' => isset ($data_route) ? $data_route->data_maps ()->where ('left_table', $data_route->table_from)->get ()->first () : null
        ]);
    }

    /**
     * 数据路径管理-保存数据路径
     *
     * @param DataRouteRequest $request
     * @return array
     */
    public function save (DataRouteRequest $request)
    {
        try {
            DataRouteRepository::add ($request->only ($this->formNames));
            return [
                'code' => 0,
                'msg' => '新增成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '新增失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据路径已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 数据路径管理-编辑数据路径
     *
     * @param int $id
     * @return View
     */
    public function edit ($id)
    {
        $this->breadcrumb [] = ['title' => '编辑数据路径', 'url' => ''];

        $model = DataRouteRepository::find ($id);
        return view ('core::admin.dataRoute.add', ['id' => $id, 'model' => $model, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据路径管理-更新数据路径
     *
     * @param DataRouteRequest $request
     * @param int $id
     * @return array
     */
    public function update (DataRouteRequest $request, $id)
    {
        $data = $request->only ($this->formNames);
        try {
            DataRouteRepository::update ($id, $data);
            return [
                'code' => 0,
                'msg' => '编辑成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '编辑失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据路径已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 数据路径管理-删除数据路径
     *
     * @param int $id
     * @return array
     */
    public function delete ($id)
    {
        try {
            DataRouteRepository::delete ($id);
            return [
                'code' => 0,
                'msg' => '删除成功',
                'redirect' => route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.index')
            ];
        } catch (\RuntimeException $e) {
            return [
                'code' => 1,
                'msg' => '删除失败：' . $e->getMessage (),
                'redirect' => false
            ];
        }
    }

}
