<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Repositories\Admin\DatabaseRepository;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function __construct ()
    {
        parent::__construct ();

        $this->breadcrumb [] = ['title' => '数据库信息', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.database.index')];
    }

    /**
     * 数据源管理-数据库
     *
     */
    public function index ()
    {
        $this->breadcrumb [] = ['title' => '数据库表列表', 'url' => ''];
        return view ('core::admin.database.index', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 数据库管理-数据库列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function list (Request $request)
    {
        $perPage = (int) $request->get ('limit', 50);
        $page = (int) $request->get ('page', 1);
        $type = $request->get ('type', '');


        if (empty ($type))
        {
            $data = DatabaseRepository::listTable ($perPage, $page - 1, $request->keyword);
        } else {
            $data = DatabaseRepository::listColumn ($perPage, $page - 1, $request->get ('table', ''), $request->keyword);
        }
        return $data;
    }

}
