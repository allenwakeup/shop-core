<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Http\Requests\Admin\AreaRequest;
use Goodcatch\Modules\Core\Repositories\Admin\AreaRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AreaController extends Controller
{
    protected $formNames = ['code', 'name', 'short', 'alias', 'display', 'description'];

    public function __construct()
    {
        parent::__construct();

        $this->breadcrumb[] = ['title' => '地区列表', 'url' => route('admin::' . module_route_prefix ('.') . 'core.area.index')];
    }

    /**
     * 主数据管理-地区列表
     *
     */
    public function index()
    {
        $this->breadcrumb[] = ['title' => '地区列表', 'url' => ''];
        return view('core::admin.area.index', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 地区管理-地区列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function list(Request $request)
    {
        $perPage = (int) $request->get('limit', 50);
        $condition = $request->only($this->formNames);

        if (! empty ($request->select))
        {
            $condition = $request->only ([ 'province_id', 'city_id' ]);
            $data = AreaRepository::select ($perPage, $condition, $request->select, $request->keyword);
        } else {
            $data = AreaRepository::list ($perPage, $condition, $request->keyword);
        }


        return $data;
    }

    /**
     * 地区管理-新增地区
     *
     */
    public function create()
    {
        $this->breadcrumb[] = ['title' => '新增地区', 'url' => ''];
        return view('core::admin.area.add', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 地区管理-保存地区
     *
     * @param AreaRequest $request
     * @return array
     */
    public function save(AreaRequest $request)
    {
        try {
            AreaRepository::add($request->only($this->formNames));
            return [
                'code' => 0,
                'msg' => '新增成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '新增失败：' . (Str::contains($e->getMessage(), 'Duplicate entry') ? '当前地区已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 地区管理-编辑地区
     *
     * @param int $id
     * @return View
     */
    public function edit($id)
    {
        $this->breadcrumb[] = ['title' => '编辑地区', 'url' => ''];

        $model = AreaRepository::find($id);
        return view('core::admin.area.add', ['id' => $id, 'model' => $model, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 地区管理-更新地区
     *
     * @param AreaRequest $request
     * @param int $id
     * @return array
     */
    public function update(AreaRequest $request, $id)
    {
        $data = $request->only($this->formNames);
        try {
            AreaRepository::update($id, $data);
            return [
                'code' => 0,
                'msg' => '编辑成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '编辑失败：' . (Str::contains($e->getMessage(), 'Duplicate entry') ? '当前地区已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 地区管理-删除地区
     *
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        try {
            AreaRepository::delete($id);
            return [
                'code' => 0,
                'msg' => '删除成功',
                'redirect' => route('admin::' . module_route_prefix ('.') . 'core.area.index')
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
