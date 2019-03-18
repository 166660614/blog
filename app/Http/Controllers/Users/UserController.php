<?php
namespace App\Http\Controllers\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
class UserController extends Controller{
    public $ktoken='u:redis:token:';
    public function login(Request $request){
        $user=$request->input('u');
        if($user){
            $token=str_random(10).$user;
            $key=$this->ktoken.$user;
            $htoken=Redis::hSet($key,'token',$token);
            Redis::expire($key,60*24*7);
            $data=[
                'errcode'=>4001,
                'Access_Token'=>$token,
            ];
        }else{
            $data=[
                'errcode'=>5200,
                'errmsg'=>'invalid userinfo',
            ];
        }
        print_r(json_encode($data));
    }
    public function center(Request $request){
        $user=$request->input('u');
        if(empty($user)){
            $data=[
                'errcode'=>5200,
                'errmsg'=>'invalid userinfo',
            ];
            print_r($data);exit;
        }
        if(empty($_SERVER['HTTP_TOKEN'])){
            $data=[
                'errcode'=>5001,
                'errmsg'=>'not find access_token'
            ];
        }else{
            $key=$this->ktoken.$user;
            $access_token=Redis::hGet($key,'token');
            if(empty($access_token)){
                $data=[
                    'errcode'=>5002,
                    'errmsg'=>'not access_token'
                ];
            }
            if($_SERVER['HTTP_TOKEN']!=$access_token){
                $data=[
                    'errcode'=>5003,
                    'errmsg'=>'invalid access_token'
                ];
            }else{
                $data=[
                    'errcode'=>4001,
                    'errmsg'=>'ok',
                ];
            }
            $data=json_encode($data);

        }
        print_r($data);
      }
      public function api(){
          $user=base64_decode($_POST['post_data']);
          $time=$_GET['t'];
          $method='AES-128-CBC';
          $salt='salt88';
          $key="key";
          $option=OPENSSL_RAW_DATA;
          $iv=substr(md5($time.$salt),5,16);
          $dec_data=openssl_decrypt($user,$method,$key,$option,$iv);
              $data=[
                  'code'=>4001,
                  'msg'=>'ok',
                  'info'=>$dec_data
              ];
         print_r(json_encode($data));
      }
}