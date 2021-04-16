<?php


/**
 *
 * This file <admin.php> was created by <PhpStorm> at <2021/2/3>,
 * and it is part of project <core>.
 * @author  Allen Li <ali@goodcatch.cn>
 */


use Illuminate\Support\Str;

Route::group (
    [
        'middleware' => 'web',
        'prefix' => 'admin',
        'as' => 'admin::',
    ],
    function ()
    {

        Route::middleware ('log:admin', 'auth:admin', 'authorization:admin')
            ->prefix (module_route_prefix ('/core'))
            ->name (module_route_prefix ('.core.'))
            ->namespace ('Admin')
            ->group (function ()
            {

                Route::get ('/', 'CoreController@index')->name ('index');
                $routes_path = __DIR__ . '/auto';
                if (is_dir ($routes_path)) {
                    foreach (new DirectoryIterator ($routes_path) as $f) {
                        if ($f->isDot ()) {
                            continue;
                        }
                        $name = $f->getPathname ();
                        if ($f->isFile () && Str::endsWith ($name, '.php')) {
                            require $name;
                        }
                    }
                }

            });
    }
);