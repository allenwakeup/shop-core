<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Model\Admin\Datasource;
use Goodcatch\Modules\Core\Repositories\Admin\DatasourceRepository;
use Goodcatch\Modules\Qwshop\Http\Resources\Admin\ModuleResource\DatasourceCollection;
use Illuminate\Http\Request;

class DatasourceController extends Controller
{

    protected $formNames = ['code', 'name', 'order', 'status', 'description', 'requires', 'options'];

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Datasource $model
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Datasource $model)
    {

        return $this->success(
            new DatasourceCollection(DatasourceRepository::list(
                $request->per_page??30,
                $request->only($this->formNames)
            )));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Datasource $model
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Datasource $model)
    {
        if($model->where('name',$request->name)->exists()){
            return $this->error(__('core::admins.area_existence'));
        }
        $model = $model->create([
            'name'      =>  $request->username,
        ]);

        return $this->success([],__('base.success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Datasource $model
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Datasource $model,$id)
    {
        $info = $model->find($id);
        return $this->success($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Datasource $model
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Datasource $model, $id)
    {
        if($model->where('name',$request->username)
            ->where('id','<>',$id)
            ->exists()
        ){
            return $this->error(__('admins.module_existence'));
        }

        $model = $model->find($id);
        $model->name = $request->name;
        $model->save();
        return $this->success([],__('base.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Datasource $model
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Datasource $model,$id)
    {
        $idArray = array_filter(explode(',',$id),function($item){
            return is_numeric($item);
        });

        $model->destroy($idArray);
        return $this->success([],__('base.success'));
    }

}
