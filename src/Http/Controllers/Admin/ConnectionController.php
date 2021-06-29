<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Events\ConnectionUpdated;
use Goodcatch\Modules\Core\Http\Requests\Admin\ConnectionRequest;
use Goodcatch\Modules\Core\Model\Admin\Connection;
use Goodcatch\Modules\Core\Http\Resources\Admin\ConnectionResource\ConnectionCollection;
use Goodcatch\Modules\Core\Repositories\Admin\ConnectionRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConnectionController extends Controller
{
    protected $formNames = [
        'Connection_id', 'name', 'description',
        'conn_type', 'tns', 'driver', 'host',  'port',
        'database', 'username', 'password',  'url', 'service_name',
        'unix_socket', 'charset', 'collation',  'prefix', 'prefix_schema',
        'strict', 'engine', 'schema', 'edition', 'server_version', 'sslmode',
        'group', 'order', 'options', 'type', 'status'
    ];

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param Connection $model
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return $this->success(
            new ConnectionCollection(ConnectionRepository::list(
                $request->per_page??30,
                $request->only($this->formNames)
            )));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ConnectionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConnectionRequest $request)
    {
        try{
            $this->success(ConnectionRepository::add($request->only($this->formNames)),__('base.success'));
        } catch (QueryException $e) {
            return $this->error([],__('base.error') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(ConnectionRepository::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ConnectionRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ConnectionRequest $request, $id)
    {
        $data = $request->only($this->formNames);

        try{
            return $this->success(ConnectionRepository::update($id, $data),__('base.success'));
        } catch (QueryException $e) {
            return $this->error([],__('base.error') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $idArray = array_filter(explode(',',$id),function($item){
            return is_numeric($item);
        });

        try{
            return $this->success(ConnectionRepository::delete($idArray),__('base.success'));
        } catch (QueryException $e) {
            return $this->error([],__('base.error') . $e->getMessage());
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

        $Connection = Connection::find ($request->Connection_id);

        $connection_config = [];

        if (isset ($Connection))
        {
            foreach (explode (',', $Connection->requires . ',' . $Connection->options) as $key)
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
                return $this->success([
                    'code' => 0,
                    'msg' => '连接成功'
                ] ,__('base.success'));
            }
        }catch (\Exception $e)
        {
            return $this->success([
                'code' => 2,
                'msg' => '测试失败 ' . $e->getMessage (),
                'redirect' => false
            ] ,__('base.success'));
        }
        return $this->success([
            'code' => 1,
            'msg' => '测试失败',
            'redirect' => false
        ] ,__('base.success'));


    }

}
