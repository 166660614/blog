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
        $data=json_encode($data);
        print_r($data);
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
    public function order(){
        //print_r($_SERVER);
        $request_url=substr(md5($_SERVER['REQUEST_URI']),0,10);
        $invalid_ip=$_SERVER['REMOTE_ADDR'];
        $redis_key="str:".$request_url.'ip:'.$invalid_ip;
        $count=Redis::incr($redis_key);
        $invalid_time=Redis::expire($redis_key,20);
        if($count>5 && $invalid_time<=20){
            //防止恶意刷api
            $data=[
                'errcode'=>5005,
                'errmsg'=>'called frequently'
            ];
            Redis::sadd('invalid_ip',$invalid_ip);//将恶意ip存入redis集合
            $ip_array=Redis::smembers('invalid_ip');//读取redis ip集合
        }else{
            $data=[
                'errcode'=>4001,
                'errmsg'=>'ok',
            ];
        }
        $data=json_encode($data);
        return $data;
    }
}