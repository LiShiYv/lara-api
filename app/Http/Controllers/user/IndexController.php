<?php
/**
 * Created by PhpStorm.
 * User: 李师雨
 * Date: 2019/3/14
 * Time: 16:38
 */
namespace App\Http\Controllers\user;
use App\Model\Cmsmodel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class IndexController extends Controller
{

    public $redis_h_u_key = 'api:h:u:';

    public function info(Request $request)
    {
        //print_r($_POST);die;
        $time=$_GET['t'];
        $key='pass';
        $api='AES-128-CBC';
        $argc=OPENSSL_RAW_DATA;
        $salt='sssss';
        $iv=substr(md5($time.$salt),5,16);

        $sign=base64_decode($_POST['sign']);
        $data=$_POST['data'];
        $pub_res=openssl_get_publickey(file_get_contents('./key/curl.key'));
        $re=openssl_verify($data,$sign,$pub_res,OPENSSL_ALGO_SHA256);
        //var_dump($re);
        if(!$re){
            echo '验签失败';die;
        }
        $post_data=base64_decode($_POST['data']);

        $dec_str=openssl_decrypt($post_data,$api,$key,$argc,$iv);
        echo $dec_str;
        if(1){
            $time=time();
            $response=[
                'erron'=>0,
                'msg'=>'ok',
                //'data'=>'this is secret'
            ];

            $iv2=substr(md5($time.$salt),5,16);
            $enc_data=openssl_encrypt(json_encode($response),$api,$key,$argc,$iv2);
            $arr=[
                't'=>$time,
                'data'=>base64_encode($enc_data)

            ];
            return $response;
            //echo json_encode($arr);
        }

//        $u = $request->input('u');
//        if ($u) {
//            $token = str_random(10);
//            $key = $this->redis_h_u_key . $u;
//            $redis_h_u_key = Redis::hSet($key, 'token', $token);
//            Redis::expire($key, 60 * 24 * 7);
//            $data = [
//                'errno' => 4001,
//                'msg' => $token
//            ];
//        } else {
//            $data = [
//                'errno' => 5200,
//                'msg' => 'HTTP_TOKEN'
//            ];
//
//        }
//        $res = json_encode($data);
//        print_r($res);

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
public function api(){

    $response = [
            'errno' => 0,
            'msg' => 'ok',

        ];
        return $response;
}
public function toreg(Request $request){
    // echo __METHOD__;
     $u_name = $request->input('u_name');

     $w = Cmsmodel::where(['u_name'=>$u_name])->first();
     if($w){
         die("用户名已存在");

     }
     $pass1=$request->input('u_pwd');
     $pass2=$request->input('u_pwd1');
     if($pass1 !==$pass2){
         die( '密码必须保持一致');
     };
     $pass = password_hash($pass1,PASSWORD_BCRYPT);
    // echo '<pre>';print_r($_POST);echo '</pre>';
     $data=[
         'u_name' => $request->input('u_name'),
         'u_email' =>$request->input('u_email'),

         'age' =>$request->input('u_age'),
         'pwd'=>$pass,
         'reg_time' =>time()
     ];
     $id=Cmsmodel::insertGetId($data);
     //var_dump($id);
     if($id){
         setcookie('u_name',$u_name,time()+86400,'/','lsy.52self.cn',false,true);

         setcookie('id',$id,time()+86400,'/','lsy.52self.cn',false,true);
        // header("Refresh:3;url=/center");
         echo '注册成功';die;
     }else{
         echo '注册失败';die;
        // header("Refresh:3;url=");
     }
 }
 public function login()
 {
      //echo __METHOD__;
     // echo '<pre>';print_r($_POST);echo '</pre>';die;
     $pass =$_POST['u_pwd'];
     $root=$_POST['u_name'];

     $id2 = Cmsmodel::where(['u_name'=>$root])->first();

     //var_dump($id2);
          if(password_verify($pass,$id2->pwd)){
            $token = substr(md5(time().mt_rand(1,99999)),10,10);
            setcookie('token',$token,time()+86400,'/','weiliang.com',false,true);
            setcookie('id',$id2->id,time()+86400,'/','weiliang.com',false,true);
            $redis_key_web='str:u:pass:'.$id2->id;
            Redis::set($redis_key_web,$token);
            Redis::expire($redis_key_web,86400);
              $response=[
                  'errno'=>0,
                  'msg'=>'登录成功'
              ];
            }else{
                  $response=[
                      'erron'=>5001,
                      'msg'=>'用户或密码不正确'
                  ];
              }
              return $response;
          }

     }

}