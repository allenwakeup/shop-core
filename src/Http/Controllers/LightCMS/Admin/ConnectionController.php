<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\LightCMS\Admin;

use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Events\ConnectionUpdated;
use Goodcatch\Modules\Core\Http\Requests\Admin\ConnectionRequest;
use Goodcatch\Modules\Core\Model\Admin\Connection;
use Goodcatch\Modules\Core\Model\Admin\Datasource;
use Goodcatch\Modules\Core\Repositories\Admin\ConnectionRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConnectionController extends Controller
{
    protected $formNames = [
        'datasource_id', 'name', 'description',
        'conn_type', 'tns', 'driver', 'host',  'port',
        'database', 'username', 'password',  'url', 'service_name',
        'unix_socket', 'charset', 'collation',  'prefix', 'prefix_schema',
        'strict', 'engine', 'schema', 'edition', 'server_version', 'sslmode',
        'group', 'order', 'options', 'type', 'status'
    ];

    public function __construct ()
    {
        parent::__construct ();

        $this->breadcrumb [] = ['title' => '连接列表', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.connection.index')];
    }

    /**
     * 数据源管理-数据库连接
     *
     */
    public function index ()
    {
        $this->breadcrumb [] = ['title' => '连接列表', 'url' => ''];
        return view ('core::admin.connection.index', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 连接管理-连接列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function list (Request $request)
    {
        $perPage = (int) $request->get ('limit', 50);
        $condition = $request->only ($this->formNames);

        $data = ConnectionRepository::list ($perPage, $condition, $request->keyword);

        return $data;
    }

    /**
     * 连接管理-新增连接
     *
     */
    public function create ()
    {
        $this->breadcrumb [] = ['title' => '新增连接', 'url' => ''];
        return view ('core::admin.connection.add', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 连接管理-连接详情
     *
     */
    public function detail ($id)
    {
        return view ('admin.detail', ['model' => Connection::find ($id)]);
    }

    /**
     * 连接管理-保存连接
     *
     * @param ConnectionRequest $request
     * @return array
     */
    public function save (ConnectionRequest $request)
    {
        try {
            ConnectionRepository::add ($request->only ($this->formNames));
            event (new ConnectionUpdated ());
            return [
                'code' => 0,
                'msg' => '新增成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '新增失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前连接已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 连接管理-编辑连接
     *
     * @param int $id
     * @return View
     */
    public function edit ($id)
    {
        $this->breadcrumb [] = ['title' => '编辑连接', 'url' => ''];

        $model = ConnectionRepository::find ($id);
        return view ('core::admin.connection.add', ['id' => $id, 'model' => $model, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 连接管理-更新连接
     *
     * @param ConnectionRequest $request
     * @param int $id
     * @return array
     */
    public function update (ConnectionRequest $request, $id)
    {
        $data = $request->only ($this->formNames);
        try {
            ConnectionRepository::update ($id, $data);
            event (new ConnectionUpdated ());
            return [
                'code' => 0,
                'msg' => '编辑成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '编辑失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前连接已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 连接管理-删除连接
     *
     * @param int $id
     * @return array
     */
    public function delete ($id)
    {
        try {
            ConnectionRepository::delete ($id);
            event (new ConnectionUpdated ());
            return [
                'code' => 0,
                'msg' => '删除成功',
                'redirect' => route ('admin::' . module_route_prefix ('.') . 'core.connection.index')
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
     * 连接管理-测试连接
     *
     * @param ConnectionRequest $request
     * @return array
     */
    public function test (ConnectionRequest $request)
    {

        $config = $request->all ();

        $datasource = Datasource::find ($request->datasource_id);

        $connection_config = [];

        if (isset ($datasource))
        {
            foreach (explode (',', $datasource->requires . ',' . $datasource->options) as $key)
            {
                $key = explode (':', trim($key), 2) [0];

                if (! empty ($key))
                {
                    $connection_config [$key] = Arr::get ($config, $key, '');
                }
            }
        }

        unset ($connection_config ['name']);

        $options = Arr::get ($connection_config, 'options');

        if (empty ($options))
        {
            unset ($connection_config ['options']);
        }


        try{
            $connection = app ('db.factory')->make ($connection_config, 'test');
            switch ($connection_config ['driver'])
            {
                case 'oracle':
                    $query = $connection->table('DUAL')->select ('*')->get ();
                    break;

                default:
                    $query = $connection->select ('select 1;');
                    break;
            }

            if (isset ($query) && count ($query) > 0)
            {
                return [
                    'code' => 0,
                    'msg' => '连接成功'
                ];
            }
        }catch (\Exception $e)
        {
            return [
                'code' => 2,
                'msg' => '测试失败 ' . $e->getMessage (),
                'redirect' => false
            ];
        }
        return [
            'code' => 1,
            'msg' => '测试失败',
            'redirect' => false
        ];


    }

}
