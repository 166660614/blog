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
          $time=$_GET['t'];
          $method='AES-128-CBC';
          $salt='salt88';
          $key="key";
          $option=OPENSSL_RAW_DATA;
          $iv=substr(md5($time.$salt),5,16);

          $data=$_POST['post_data'];
          $sign=base64_decode($_POST['sign']);
          $pub_secret=openssl_get_publickey(file_get_contents('./pub.key'));
          $res=openssl_verify($data,$sign,$pub_secret,OPENSSL_ALGO_SHA256);
          //var_dump($res);exit;
          if(!$res){
              die('验签失败');
          }else{
              $post_data=base64_decode($_POST['post_data']);
              $dec_data=openssl_decrypt($post_data,$method,$key,$option,$iv);
              $res_data=[
                  'code'=>4001,
                  'msg'=>'ok',
                 // 'info'=>$dec_data,
                  'res_time'=>time(),
              ];
              $res_data=json_encode($res_data);
              $res_method='AES-128-CBC';
              $res_salt='salt99';
              $res_key="reskey";
              $res_option=OPENSSL_RAW_DATA;
              $res_iv=substr(md5(time().$res_salt),5,16);
              $res_info=base64_encode(openssl_encrypt($res_data,$res_method,$res_key,$res_option,$res_iv));
              print_r(json_encode($res_info),true);
          }
      }
      public function hapi(){
        $value=$_POST['value'];
        if($value){
            $data=[
                'errcode'=>4001,
                'errmsg'=>'ok'
            ];
        }else{
            $data=[
                'errcode'=>5001,
                'errmsg'=>'error'
            ];
        }
        return $data;
      }
}