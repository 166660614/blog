<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Redis;
use Closure;
class IllegalMiddle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
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
            return json_encode($data);
        }
        return $next($request);
    }
}
