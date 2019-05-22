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
Route::get('/aa', function () {
    echo urlencode("http://1809zhanghaibo.comcto.com/wxweb/hd");
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
//微信第一次访问
Route::get('/weixin/valid',"WxController@valid");
//微信推送消息
Route::post('/weixin/valid',"WxController@event");
//主页
Route::get('/weixin/index',"weixin\CarController@index");
//商品详情
Route::get('/weixin/detail',"weixin\CarController@detail");
//浏览历史
Route::get('/weixin/history',"weixin\CarController@history");
//添加购物车
Route::get('/weixin/addCar/{goods_id?}',"weixin\CarController@addCar")->middleware('checkLogin');
//购物车列表
Route::get('/weixin/car',"weixin\CarController@car")->middleware('checkLogin');
//购物车结算生成订单
Route::post('/weixin/success',"weixin\CarController@success")->middleware('checkLogin');
//微信支付
Route::get('/weixin/wxPay/{order_sn}',"weixin\WxPayController@test")->middleware('checkLogin');
//验证微信支付
Route::get('/weixin/paystatus',"weixin\WxPayController@paystatus");
//支付成功跳转页面
Route::get('/weixin/supay',"weixin\WxPayController@supay")->middleware('checkLogin');
//微信支付回调地址
Route::post('/weixin/pay/notify','weixin\WxPayController@notify_url');
//订单页面
Route::get('/weixin/order',"weixin\CarController@order")->middleware('checkLogin');
//微信jssdk
Route::get('weixin/jssdk',"weixin\JsSdkController@jssdk");
//图片上传
Route::get('weixin/upload',"weixin\JsSdkController@upload");

//删除超过三十分钟未支付的订单
Route::get('weixin/delorder','crontab\CrontabController@delOrder');
//网页授权回调地址
Route::get('wxweb/hd',"WxwebController@hd");
//获取带参数的二维码
Route::get('weixin/activ',"ActivityController@index");
//最新活动页面
Route::get('weixin/view',"ActivityController@view");
//创建微信菜单
Route::get('weixin/menu','WxController@create_menu');
//最新福利授权回调
Route::get('web/hd','WxController@hd');
//微信签到
Route::get('weixin/signIn','WxController@signIn');
//将用户搜索的商品名存储数据库
Route::get('weixin/savegoodsname','WxController@savegoodsname');
//设置所属行业
Route::get('weixin/set','WxController@set');
//微信群发消息
Route::get('/send_valid','WxController@send_valid');



