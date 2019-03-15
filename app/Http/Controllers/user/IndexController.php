<?php
/**
 * Created by PhpStorm.
 * User: 李师雨
 * Date: 2019/3/14
 * Time: 16:38
 */
namespace App\Http\Controllers\user;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class IndexController extends Controller
{

    public $redis_h_u_key = 'api:h:u:';

    public function info(Request $request)
    {
        // echo 1111;
        $u = $request->input('u');
        if ($u) {
            $token = str_random(10);
            $key = $this->redis_h_u_key . $u;
            $redis_h_u_key = Redis::hSet($key, 'token', $token);
            Redis::expire($key, 60 * 24 * 7);
            $data = [
                'errno' => 4001,
                'msg' => $token
            ];
        } else {
            $data = [
                'errno' => 5200,
                'msg' => 'HTTP_TOKEN'
            ];

        }
        $res = json_encode($data);
        print_r($res);

    }

    public function uCenter(Request $request)
    {
        $u = $request->input('u');
        if (empty($_SERVER['HTTP_TOKEN'])) {
            $response = [
                'errno' => 50001,
                'msg' => 'Token Require!!'

            ];
        } else {

            $key = $this->redis_h_u_key . $u;
            $token = Redis::hGet($key, 'token');
            //print_r($_SERVER['HTTP_TOKEN']);die;
            if ($_SERVER['HTTP_TOKEN'] != $token) {
                $response = [
                    'errno' => 50000,
                    'msg' => 'Not Token Require!!'

                ];
            } else {

                $response = [
                    'errno' => 0,
                    'msg' => 'ok',

                ];
            }


            $response = json_encode($response);
        }
        print_r($response);
    }

//防刷机制
public function apiRedis()
{
//    //print_r($_SERVER);
//    $url=$_SERVER['REQUEST_URI'];
//   // echo md5($url);
//    $hash=substr(md5($url),0,10);
//  //  echo $hash;
//    $ip=$_SERVER['REMOTE_ADDR'];
//   // echo $ip;
//    $redis_key='str:'.$hash.':'.$ip;
//    //echo $redis_key;
//    $num=Redis::incr($redis_key);
//    Redis::expire($redis_key,60);
//    echo 'conten:'.$num;
//    if($num>5){
//        $response=[
//            'erron'=>40003,
//            'msg'=>' Notice!!!'
//
//            ];
//        Redis::expire($redis_key,600);
//        //记录非法请求
//        $redis_invalid='s:invalid:ip';
//        Redis::sAdd($redis_invalid,$ip);
//
//    }else{
        $response=[
          'erron'=>0,
            'msg'=>'ok',
        ];
//    }
   return $response;
//}

}
}