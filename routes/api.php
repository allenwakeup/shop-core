<?php

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
                        'data_maps'=>'DataMapController', // 数据映射
                    ]);
                    Route::get('/databases', 'DatabaseController@index')->name('databases.index'); // 数据库
                    Route::post('/connections/test', 'ConnectionController@test')->name('connections.test'); // 数据库

                    Route::post('/dataMaps/{id}/assignment/{left_id}/save', 'DataMapController@saveAssignment')->name('data_maps.assignment.store'); // 数据映射
                    Route::delete('/dataMaps/{id}/assignment/{left_id}/delete', 'DataMapController@deleteAssignment')->name('data_maps.assignment.destroy'); // 数据映射
                    Route::get('/dataMaps/{id}/assignment/select/{left_id}', 'DataMapController@selectAssignment')->name('data_maps.assignment.show'); // 数据映射
                    Route::get('/dataMaps/{id}/assignment', 'DataMapController@assignment')->name('data_maps.assignment.index'); // 数据映射
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


