<?php

use Illuminate\Http\Request;
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

Route::prefix('Admin')->group(function(){
    Route::namespace('Admin')->prefix('goodcatch')->group(function(){
        Route::prefix(module_route_prefix())->group(function(){
            Route::prefix('core')->name('core.')->group(function(){
                Route::group(['middleware'=>'jwt.admin'], function(){
                    Route::apiResources([
                        'areas'=>'AreaController', // 区域
                        'datasources'=>'DatasourceController', // 区域
                        'connections'=>'ConnectionController', // 连接
                    ]);
                    Route::get('/databases', 'DatabaseController@index')->name('databases.index'); // 数据库
                    Route::get('/connections/test', 'DatabaseController@test')->name('connections.test'); // 数据库
                });
            });
        });
    });

    /**
     *
     * @author hg <364825702@qq.com>
     * 商城商家后台 路由
     *
     */
    Route::prefix('Seller')->namespace('Seller')->group(function(){
        Route::group(['middleware'=>'jwt.user'],function(){

        });
    });

    /**
     *
     * @author hg <364825702@qq.com>
     * 商城PC端 路由
     *
     */
    Route::namespace('Home')->group(function(){

        Route::group(['middleware'=>'jwt.user'],function(){

        });
    });

    /**
     *
     * @author hg <364825702@qq.com>
     * 商城App端 路由
     *
     */
    Route::prefix('App')->namespace('App')->group(function(){

        Route::group(['middleware'=>'jwt.user'],function(){

        });
    });
});


