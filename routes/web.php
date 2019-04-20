<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
//主页
Route::get('/weixin/index',"weixin\CarController@index");
//添加购物车
Route::get('/weixin/addCar/{goods_id?}',"weixin\CarController@addCar");
//购物车列表
Route::get('/weixin/car',"weixin\CarController@car");
//购物车结算生成订单
Route::post('/weixin/success',"weixin\CarController@success");
//微信支付
Route::get('/weixin/wxPay/{order_sn}',"weixin\WxPayController@test");
//验证微信支付
Route::get('/weixin/paystatus',"weixin\WxPayController@paystatus");
//支付成功跳转页面
Route::get('/weixin/supay',"weixin\WxPayController@supay");
