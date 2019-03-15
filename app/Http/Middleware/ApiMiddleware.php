<?php
/**
 * Created by PhpStorm.
 * User: 李师雨
 * Date: 2019/3/15
 * Time: 11:22
 */

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Redis;
class ApiMiddleware
{
    public function handle($request, Closure $next)
    {
        //print_r($_SERVER);
        $url=$_SERVER['REQUEST_URI'];
        // echo md5($url);
        $hash=substr(md5($url),0,10);
        //  echo $hash;
        $ip=$_SERVER['REMOTE_ADDR'];
        // echo $ip;
        $redis_key='str:'.$hash.':'.$ip;
        //echo $redis_key;
        $num=Redis::incr($redis_key);
        Redis::expire($redis_key,60);
        if($num>5){
            $response = [
                'erron' => 50003,
                'msg' => 'Notice!!!'

            ];
           // var_dump($response);
            Redis::expire($redis_key, 600);
            //记录非法请求
            $redis_invalid = 's:invalid:ip';
            Redis::sAdd($redis_invalid, $ip);
          return json_encode($response);

        }

        return $next($request);
    }

}