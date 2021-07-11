<?php

use Goodcatch\Modules\Core\Repositories\Admin\DataMapRepository;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 *
 * @author allen <ali@goodcatch.cn>
 * 商城后台 路由
 *
 */
Route::prefix('Admin')->group(function(){
    Route::namespace('Admin')->prefix('goodcatch')->group(function(){
        Route::prefix(module_route_prefix())->group(function(){
            Route::prefix('core')->name('core.')->group(function(){
                Route::group(['middleware'=>'jwt.admin'], function(){
                    Route::apiResources([
                        'areas'=>'AreaController', // 区域
                        'datasources'=>'DatasourceController', // 区域
                        'connections'=>'ConnectionController', // 连接
                        'schedules'=>'ScheduleController', // 计划与任务
                        'data_routes'=>'DataRouteController', // 数据路径
                        'data_maps'=>'DataMapController', // 数据映射
                    ]);
                    Route::get('/databases', 'DatabaseController@index')->name('databases.index'); // 数据库
                    Route::post('/connections/test', 'ConnectionController@test')->name('connections.test'); // 数据库
                    Route::get('/schedules/{id}/logs', 'ScheduleController@logs')->name('schedules.logs'); // 计划任务日志

                    DataMapRepository::loadEnabledFromCache()->each(function($item){
                        $data_map_id = $item['id'];
                        Route::post("/data_maps_{$data_map_id}/{id}/assignment/{left_id}/store", 'DataMapController@storeAssignment')->name("data_maps.{$data_map_id}.store");
                        Route::delete("/data_maps_{$data_map_id}/{id}/assignment/{left_id}/destroy", 'DataMapController@destroyAssignment')->name("data_maps.{$data_map_id}.destroy");
                        Route::get("/data_maps_{$data_map_id}/{id}/assignment/{left_id}/source", 'DataMapController@assignmentSource')->name("data_maps.{$data_map_id}.source");
                        Route::get("/data_maps_{$data_map_id}/{id}/assignment/{left_id}/target", 'DataMapController@assignmentTarget')->name("data_maps.{$data_map_id}.target");
                        Route::get("/data_maps_{$data_map_id}/{id}/assignment", 'DataMapController@showAssignment')->name("data_maps.{$data_map_id}.show");
                        Route::get("/data_maps_{$data_map_id}/{id}/assignment/{table_left}", 'DataMapController@assignment')->name("data_maps.{$data_map_id}.index");
                    });
                });
            });
        });
    });

    /**
     *
     * @author allen <ali@goodcatch.cn>
     * 商城商家后台 路由
     *
     */
    Route::prefix('Seller')->namespace('Seller')->group(function(){
        Route::group(['middleware'=>'jwt.user'],function(){

        });
    });

    /**
     *
     * @author allen <ali@goodcatch.cn>
     * 商城PC端 路由
     *
     */
    Route::namespace('Home')->group(function(){

        Route::group(['middleware'=>'jwt.user'],function(){

        });
    });

    /**
     *
     * @author allen <ali@goodcatch.cn>
     * 商城App端 路由
     *
     */
    Route::prefix('App')->namespace('App')->group(function(){

        Route::group(['middleware'=>'jwt.user'],function(){

        });
    });
});


