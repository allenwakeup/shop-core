<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Goodcatch\Modules\Core\Http\Requests\Admin\AreaRequest;
use Goodcatch\Modules\Core\Repositories\Admin\AreaRepository;
use Goodcatch\Modules\Core\Http\Resources\Admin\AreaResource\AreaCollection;
use Illuminate\Database\QueryException;

class AreaController extends Controller
{

    protected $formNames = ['code', 'name', 'short', 'alias', 'display', 'description', 'province_id', 'city_id'];

    /**
     * Display a listing of the resource.
     *
     * @param AreaRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(AreaRequest $request)
    {
        $type = $request->type;
        $conditions = $request->only($this->formNames);

        if($type === 'selector'){
            return $this->success(AreaRepository::select($conditions, $request->get($type), $request->keyword));
        } else {
            return $this->success(
                new AreaCollection(AreaRepository::list(
                    $request->per_page??30,
                    $conditions
                )));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AreaRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AreaRequest $request)
    {
        try{
            return AreaRepository::add($request->only($this->formNames));
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
        return $this->success(AreaRepository::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AreaRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(AreaRequest $request,$id)
    {
        $data = $request->only($this->formNames);

        try{
            AreaRepository::update($id, $data);
            return $this->success([],__('base.success'));
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
            AreaRepository::delete($idArray);
            return $this->success([],__('base.success'));
        } catch (QueryException $e) {
            return $this->error([],__('base.error') . $e->getMessage());
        }
    }
}
