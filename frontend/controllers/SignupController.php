<?php

namespace frontend\controllers;

use Yii;
use common\models\Signup;
use common\models\User;
use common\models\Code;
use common\models\Meeting;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
header("Access-Control-Allow-Origin:*");

/**
 * SignupController implements the CRUD actions for Signup model.
 */
class SignupController extends BaseController
{

    /**
     * Lists all Signup models.
     * @return mixed
     */
    public function actionIndex()
    {

        $userphone = Yii::$app->request->post('userphone');
        $userpwd = Yii::$app->request->post('userpwd');
        if(empty($userphone)){
            return self::Json(201,'手机号不能为空!');
        }

        if(!preg_match("/^1[34578]{1}\d{9}$/",$userphone)){
            return self::Json(201,'请输入正确的手机号!');
        }
        if(empty($userpwd)){
            return self::Json(201,'密码不能为空!');
        }

        $user_model = User::find()->where(['userphone' => $userphone])->asArray()->one();
        if(empty($user_model)){
            return self::Json(201,'该用户不存在!');
        }else if($user_model['userpwd'] != md5($userpwd)){
            return self::Json(201,'密码不正确!');
        }else if($user_model['userpwd'] == md5($userpwd)){
            Yii::$app->session['user_id'] = $user_model['id'];
            return self::Json(200,'登录成功!');
        }

    }
}
