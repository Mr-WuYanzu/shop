<?php

namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Content;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
class FodderController extends Controller
{
    use HasResourceActions;
   public function index(Content $content){
       return $content
           ->header('素材管理')
           ->description('素材添加')
           ->body(view('admin.weixin.fodder'));
   }
   public function fodderAdd(Request $request){
       $img_name = $this->upload($request,'img');
       if($img_name){
           $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.getAccessToken().'&type=image';
           $client = new Client();
           $response = $client->request('post',$url,[
               'multipart' => [
                   [
                       'name' => 'media',
                       'contents' => fopen('../storage/app/'.$img_name, 'r'),
                   ]
               ]
           ]);

           $json =  json_decode($response->getBody(),true);
//            dd($json);
           if(isset($json['media_id'])){
               DB::table('p_wx_fodder')->insert(['media_id'=>$json['media_id']]);
           }
       }
   }
   //文件上传
   public function upload($request,$imgName){
       if ($request->hasFile($imgName) && $request->file($imgName)->isValid()) {
           $photo = $request->file($imgName);
           $path='uploads/';
           $store_result = $photo->store($path.date('Ymd'));
           return $store_result;
       }
       return false;
   }
}
