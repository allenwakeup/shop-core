<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Http\Requests\Admin\DatasourceRequest;
use Goodcatch\Modules\Core\Repositories\Admin\DatasourceRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DatasourceController extends Controller
{
    protected $formNames = ['code', 'name', 'order', 'status', 'description', 'requires', 'options'];

    public function __construct ()
    {
        parent::__construct ();

        $this->breadcrumb [] = ['title' => '数据源列表', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.datasource.index')];
    }

    /**
     * 数据源管理-数据源
     *
     */
    public function index ()
    {
        $this->breadcrumb [] = ['title' => '数据源列表', 'url' => ''];
        return view ('core::admin.datasource.index', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据源管理-数据源列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function list (Request $request)
    {
        $perPage = (int) $request->get ('limit', 50);
        $condition = $request->only ($this->formNames);

        $data = DatasourceRepository::list ($perPage, $condition, $request->keyword);

        return $data;
    }

    /**
     * 数据源管理-新增数据源
     *
     */
    public function create ()
    {
        $this->breadcrumb [] = ['title' => '新增数据源', 'url' => ''];
        return view ('core::admin.datasource.add', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据源管理-保存数据源
     *
     * @param DatasourceRequest $request
     * @return array
     */
    public function save (DatasourceRequest $request)
    {
        try {
            DatasourceRepository::add ($request->only ($this->formNames));
            return [
                'code' => 0,
                'msg' => '新增成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '新增失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据源已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 数据源管理-编辑数据源
     *
     * @param int $id
     * @return View
     */
    public function edit ($id)
    {
        $this->breadcrumb [] = ['title' => '编辑数据源', 'url' => ''];

        $model = DatasourceRepository::find ($id);
        return view ('core::admin.datasource.add', ['id' => $id, 'model' => $model, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据源管理-更新数据源
     *
     * @param DatasourceRequest $request
     * @param int $id
     * @return array
     */
    public function update (DatasourceRequest $request, $id)
    {
        $data = $request->only ($this->formNames);
        try {
            DatasourceRepository::update ($id, $data);
            return [
                'code' => 0,
                'msg' => '编辑成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '编辑失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据源已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 数据源管理-删除数据源
     *
     * @param int $id
     * @return array
     */
    public function delete ($id)
    {
        try {
            DatasourceRepository::delete ($id);
            return [
                'code' => 0,
                'msg' => '删除成功',
                'redirect' => route ('admin::' . module_route_prefix ('.') . 'core.datasource.index')
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
