<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Http\Requests\Admin\DatasourceRequest;
use Goodcatch\Modules\Core\Repositories\Admin\DatasourceRepository;
use Goodcatch\Modules\Core\Http\Resources\Admin\DatasourceResource\DatasourceCollection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class DatasourceController extends Controller
{

    protected $formNames = ['code', 'name', 'order', 'status', 'description', 'requires', 'options'];

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return $this->success(
            new DatasourceCollection(DatasourceRepository::list(
                $request->per_page??30,
                $request->only($this->formNames),
                $request->keyword
            )));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DatasourceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(DatasourceRequest $request)
    {
        try{
            return $this->success(DatasourceRepository::add($request->only($this->formNames)),__('base.success'));
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
        return $this->success(DatasourceRepository::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DatasourceRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(DatasourceRequest $request, $id)
    {
        $data = $request->only($this->formNames);

        try{
            return $this->success(DatasourceRepository::update($id, $data),__('base.success'));
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
            return $this->success(DatasourceRepository::delete($idArray),__('base.success'));
        } catch (QueryException $e) {
            return $this->error([],__('base.error') . $e->getMessage());
        }
    }

}
