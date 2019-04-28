<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    Route::resource('goods',GoodsController::class);
    Route::resource('user',UserController::class);
    Route::resource('order',OrderController::class);
    Route::resource('Manage',ManageController::class);//素材管理
    Route::get('message','MessageController@index');
    Route::get('messageAdd','MessageController@add');
    //素材添加页面
    Route::get('fodder','FodderController@index');
    //素材添加执行
    ROute::post('fodderAdd','FodderController@fodderAdd');
});
