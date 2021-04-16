<?php


namespace Goodcatch\Modules\Core\Http\Controllers\LightCMS\Admin;


use Goodcatch\Modules\Core\Http\Requests\Admin\DepartmentRequest;
use Goodcatch\Modules\Core\Repositories\Admin\DepartmentRepository;
use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    protected $formNames = ['name', 'pid', 'order'];

    public function __construct ()
    {
        parent::__construct ();

        $this->breadcrumb [] = ['title' => '部门列表', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.department.index')];
    }

    /**
     * 主数据管理-部门列表
     *
     */
    public function index (DepartmentRequest $request)
    {
        $pid = (int) $request->get('pid', 0);

        if ($pid > 0)
        {
            $dep = DepartmentRepository::find($pid);
            if ($dep)
            {
                $deps = [];
                while ($dep)
                {
                    $deps [] = ['title' => $dep->name, 'url' => route('admin::' . module_route_prefix ('.') . 'core.department.index') . ($dep->pid > 0 ? ('?pid='. $dep->pid) : '') ];
                    $dep = $dep->parent;
                }
                $this->breadcrumb = array_merge ($this->breadcrumb, array_reverse ($deps));
            }
        }
        return view ('core::admin.department.index', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 主数据管理-部门列表数据接口
     *
     * @param DepartmentRequest $request
     * @return array
     */
    public function list (DepartmentRequest $request)
    {
        $perPage = (int) $request->get ('limit', 50);
        $action = $request->get ('action');
        $condition = $request->only ($this->formNames);

        if (isset ($condition ['pid'])) {
            $condition ['pid'] = ['=', $condition ['pid']];
        } else {
            if ($action !== 'search') {
                $condition['pid'] = ['=', 0];
            }
        }

        if ($request->type === 'tree')
        {
            if (!empty ($request->keyword) && is_numeric ($request->keyword))
            {
                $data = DepartmentRepository::tree2 (intval ($request->keyword));
            } else {
                $data = DepartmentRepository::tree2 (0);
            }
            return [
                'code' => 0,
                'data' => $data->values ()->all (),
                'msg' => '请求成功'
            ];
        } else if ($request->type === 'select') {
            $data = DepartmentRepository::selectTree ($request->pid ?? 0);
        }
        else {
            $data = DepartmentRepository::list ($perPage, $condition);
        }


        return $data;
    }

    /**
     * 主数据管理-部门树型列表数据接口
     *
     * @param DepartmentRequest $request
     * @return array
     */
    public function tree (DepartmentRequest $request)
    {
        return DepartmentRepository::tree ();
    }

    /**
     * 主数据管理-新增部门
     *
     */
    public function create ()
    {
        $this->breadcrumb[] = ['title' => '新增部门', 'url' => ''];
        return view('core::admin.department.add', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 主数据管理-保存部门
     *
     * @param DepartmentRequest $request
     * @return array
     */
    public function save (DepartmentRequest $request)
    {
        try {
            DepartmentRepository::add ($request->only ($this->formNames));
            return [
                'code' => 0,
                'msg' => '新增成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '新增失败：' . (Str::contains($e->getMessage(), 'Duplicate entry') ? '当前部门已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 主数据管理-编辑部门
     *
     * @param int $id
     * @return View
     */
    public function edit($id)
    {
        $this->breadcrumb[] = ['title' => '编辑部门', 'url' => ''];

        $model = DepartmentRepository::find($id);
        return view('core::admin.department.add', ['id' => $id, 'model' => $model, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 主数据管理-更新部门
     *
     * @param DepartmentRequest $request
     * @param int $id
     * @return array
     */
    public function update (DepartmentRequest $request, $id)
    {
        $data = $request->only ($this->formNames);
        try {
            DepartmentRepository::update ($id, $data);
            return [
                'code' => 0,
                'msg' => '编辑成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '编辑失败：' . (Str::contains($e->getMessage(), 'Duplicate entry') ? '当前部门已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 主数据管理-删除部门
     *
     * @param int $id
     * @return array
     */
    public function delete ($id)
    {
        try {
            DepartmentRepository::delete ($id);
            return [
                'code' => 0,
                'msg' => '删除成功',
                'redirect' => route ('admin::' . module_route_prefix ('.') . 'core.department.index')
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