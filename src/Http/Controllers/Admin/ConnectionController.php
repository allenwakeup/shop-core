<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Events\ConnectionUpdated;
use Goodcatch\Modules\Core\Http\Requests\Admin\ConnectionRequest;
use Goodcatch\Modules\Core\Http\Resources\Admin\ConnectionResource\ConnectionCollection;
use Goodcatch\Modules\Core\Model\Admin\Datasource;
use Goodcatch\Modules\Core\Repositories\Admin\ConnectionRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
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
            $data = ConnectionRepository::add($request->only($this->formNames));
            event (new ConnectionUpdated ());
            return $this->success($data,__('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据已存在' : '其它错误'));
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
            $res = ConnectionRepository::update($id, $data);
            event (new ConnectionUpdated ());
            return $this->success($res,__('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据已存在' : '其它错误'));
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
            $data = ConnectionRepository::delete($idArray);
            event (new ConnectionUpdated ());
            return $this->success($data, __('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . $e->getMessage());
        }
    }

    /**
     * test connection configuration by connecting to this data source
     *
     * @param ConnectionRequest $request
     * @return array
     */
    public function test (ConnectionRequest $request)
    {

        $config = $request->only($this->formNames);

        $datasource = Datasource::find ($request->datasource_id);

        $datasource_config = [];

        if (isset ($datasource))
        {
            foreach (explode (',', $datasource->requires . ',' . $datasource->options) as $key)
            {
                $key = explode (':', trim($key), 2) [0];

                if (! empty ($key))
                {
                    $datasource_config [$key] = Arr::get ($config, $key, '');
                }
            }
        }

        unset ($datasource_config ['name']);

        $options = Arr::get ($datasource_config, 'options');

        if (empty ($options))
        {
            unset ($datasource_config ['options']);
        }

        try{
            $connection = app ('db.factory')->make ($datasource_config, 'test');
            switch ($datasource_config ['driver'])
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
                return $this->success([] , __('core::admins.connection_test_successful'));
            }
        }catch (\Exception $e) {
            return $this->error(__('core::admins.connection_test_failed') . $e->getMessage ());
        }
        return $this->error(__('core::admins.connection_test_failed'));
    }

}
