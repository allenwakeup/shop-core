<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Events\DataMapUpdated;
use Goodcatch\Modules\Core\Http\Requests\Admin\DataMapRequest;
use Goodcatch\Modules\Core\Http\Resources\Admin\DataMapResource\DataMapCollection;
use Goodcatch\Modules\Core\Jobs\SyncDataMappingData;
use Goodcatch\Modules\Core\Model\Admin\Eloquent;
use Goodcatch\Modules\Core\Repositories\Admin\DataMapRepository;
use Goodcatch\Modules\Core\Model\Admin\DataMap;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DataMapController extends Controller
{
    protected $formNames = ['data_route_id', 'left', 'left_table', 'left_tpl', 'right',
        'right_table', 'right_tpl', 'relationship', 'description', 'name', 'table',
        'through', 'first_key', 'second_key', 'foreign_key', 'owner_key',
        'local_key', 'second_local_key', 'foreign_pivot_key', 'related_pivot_key',
        'parent_key', 'related_key', 'relation', 'status'
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
            new DataMapCollection(DataMapRepository::list(
                $request->per_page??30,
                $request->only($this->formNames),
                $request->keyword
            )));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DataMapRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(DataMapRequest $request)
    {
        try{
            $added = DataMapRepository::add($request->only($this->formNames));
            event (new DataMapUpdated ());
            return $this->success($added,__('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据映射已存在' : '其它错误'));
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
        return $this->success(DataMapRepository::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DataMapRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(DataMapRequest $request, $id)
    {
        $data = $request->only($this->formNames);

        try{
            $updated = DataMapRepository::update($id, $data);
            event (new DataMapUpdated ());
            return $this->success($updated,__('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据映射已存在' : '其它错误'));
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
            $deleted = DataMapRepository::delete($idArray);
            event (new DataMapUpdated ());
            return $this->success($deleted,__('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function showAssignment ($id)
    {

        $dataMap = DataMap::find ($id);

        $data = [
            'action' => [
                'store' => route ('core.data_maps.assignment.store', ['id' => $id, 'left_id' => ':left_id']),
                'destroy' => route ('core.data_maps.assignment.destroy', ['id' => $id, 'left_id' => ':left_id']),
            ],
        ];

        if (isset ($dataMap))
        {
            $data ['title'] = $dataMap->left . '-' . $dataMap->right . '映射';
            $data ['id'] = $id;
            $data ['mapping'] = $dataMap->left_table . '-' . $dataMap->right_table;
            $data ['assignment'] = $dataMap;
            $data ['api'] = route ( 'core.data_maps.assignment.index', ['id' => $id, 'left_id' => $dataMap->left_table]);

        }
        return $this->success($data,__('base.success'));

    }


    /**
     * data mapping left model
     *
     * @param $id datamap ID
     * @param $left_id mapping id
     * @param Request $request
     * @return array
     */
    public function assignment (Request $request, $id, $left_id) {

        $dataMap = DataMap::find ($id);

        try{
            $data = isset ($dataMap) ? DataMapRepository::select (
                $request->per_page??30,
                $dataMap,
                $left_id,
                $request->keyword)
                : [];
            return $this->success($data,__('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . $e->getMessage());
        }


    }

    /**
     * map left model to right model
     *
     * @param \Illuminate\Http\Request $request
     * @param $id datamap ID
     * @param $left_id mapping id
     * @return array results
     */
    public function storeAssignment (Request $request, $id, $left_id) {
        $ids = $request->input ('id.*');
        if (isset ($ids) && count ($ids) > 0)
        {
            $dataMap = DataMap::find ($id);

            if (isset ($dataMap) && $dataMap->status === DataMap::STATUS_ENABLE)
            {
                try {

                    $attached = (new Eloquent)->setDataMapTable ($dataMap->left_table)
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
                        'data_route' => isset ($dataMap->dataRoute) ? $dataMap->dataRoute->toArray () : [],
                        'connection' => isset ($dataMap->dataRoute)
                            ? (
                            isset ($dataMap->dataRoute->connection)
                                ? $dataMap->dataRoute->connection->name
                                : ''
                            )
                            : ''
                    ])));

                    $this->success($attached);

                } catch (\RuntimeException $e) {
                    return $this->error(__('base.error') . $e->getMessage());
                }
            }
        }

        return $this->error(__('base.error'));

    }

    /**
     * detach model mapping
     *
     * @param \Illuminate\Http\Request $request
     * @param $id datamap ID
     * @param $left_id mapping id
     * @return array result
     */
    public function destoryAssignment (Request $request, $id, $left_id) {
        $ids = $request->input ('id.*');
        if (isset ($ids) && count ($ids) > 0)
        {
            $dataMap = DataMap::find ($id);
            if (isset ($dataMap) && $dataMap->status === DataMap::STATUS_ENABLE)
            {
                try {
                    $detached = (new Eloquent)->setDataMapTable ($dataMap->left_table)
                        ->firstWhere ($dataMap->parent_key, $left_id)
                        ->setDataMapTable ($dataMap->left_table)
                        ->getDataMapping ($dataMap->right_table)
                        ->detach ($ids);

                    dispatch (new SyncDataMappingData (array_merge ($dataMap->toArray (), [
                        'title' => $dataMap->title,
                        'left_id' => $left_id,
                        'detach' => true,
                        'data_route' => isset ($dataMap->dataRoute) ? $dataMap->dataRoute->toArray () : [],
                        'connection' => isset ($dataMap->dataRoute)
                            ? (isset ($dataMap->dataRoute->connection)
                                ? $dataMap->dataRoute->connection->name
                                : '')
                            : ''
                    ])));

                    return $this->success($detached);
                } catch (\RuntimeException $e) {
                    return $this->error(__('base.error') . $e->getMessage());
                }
            }
        }

        return $this->error(__('base.error'));
    }

}
