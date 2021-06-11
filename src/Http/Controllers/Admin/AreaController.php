<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Goodcatch\Modules\Core\Model\Admin\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Area $model
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Area $model)
    {
        $list = $model->orderBy('id','desc')->paginate($request->per_page??30);
        return $this->success(new AreaCollection($list) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Area $model
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Area $model)
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
     * @param Area $model
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Area $model,$id)
    {
        $info = $model->find($id);
        return $this->success($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Area $model
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Area $model, $id)
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
     * @param Area $model
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $model,$id)
    {
        $idArray = array_filter(explode(',',$id),function($item){
            return is_numeric($item);
        });

        $model->destroy($idArray);
        return $this->success([],__('base.success'));
    }
}
