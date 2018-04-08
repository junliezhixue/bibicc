<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
//use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\rest\Controller;
class BaseController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['POST', 'PUT'],
                // Allow only POST and PUT methods
                'Access-Control-Request-Headers' => ['*'],
                // Allow only headers 'X-Wsse'
                'Access-Control-Allow-Credentials' => null,
                // Allow OPTIONS caching
                'Access-Control-Max-Age' => 3600,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ]
        ];
        // $behaviors['ContentNegotiator'] = [
        //     'class' => ContentNegotiator::className(),
        //     'formats' => [
        //         'application/json' => Response::FORMAT_JSON
        //     ]
        // ];
        return $behaviors;
    }

    public function init()
    {
        // exit('活动即将开启');
        $this->layout = false;
        $this->enableCsrfValidation = false;
    }
	public static function Json($code='', $message='', $data=array(),$new_arr=array())
    {
        $result=[
            'code'=>$code,
            'message'=>$message,
            'data'=>$data,
            'new_arr' => $new_arr,
        ];
        exit(json_encode($result));
    }

    //短信
    public function actionNewinfo($tel){
        $order_num = mt_rand(1000,10000);
        $pass = strtoupper(md5(804696));
        $content="【验证码】:".$order_num;
        $contents=base64_encode($content);
        $url="";
        $info=file_get_contents($url);
        if($info>1){
            //echo $info;exit;
            return  (string)$order_num;
        }else{
            return false;
        }
    }

}