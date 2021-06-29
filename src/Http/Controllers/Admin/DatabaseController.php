<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Repositories\Admin\DatabaseRepository;
use Goodcatch\Modules\Core\Http\Resources\Admin\DatabaseResource\DatabaseCollection;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $table = $request->get ('table', '');
        $keyword = $request->get ('keyword', '');

        return $this->success(new DatabaseCollection(empty($table)
            ? DatabaseRepository::listTable($keyword)
            : DatabaseRepository::listColumn($table, $keyword)));
    }

}
