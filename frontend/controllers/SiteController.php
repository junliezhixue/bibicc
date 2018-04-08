<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use common\Taskrun;
use common\Taskclient;
use frontend\controllers\BaseAPIController;

/**
 * Site controller
 */
class SiteController extends BaseAPIController
{
    public function actionIndex()
    {
	return ['123456'];
        return $this->render('index');
    }

    public function actionSeverup()
    {
        try {
            $serv = new \swoole_server('0.0.0.0', 9501);
            $serv->set([
                    'worker_num'                => 1,
                    'max_request'               => 30,
                    'backlog'                   => 20,
                    'log_file'                  => '/data/log/swoole.log',
                    'heartbeat_check_interval'  => 30,
                    'heartbeat_idle_time'       => 60
                ]);
            $serv->on('connect', function($con, $fd) {
                $con->tick(2000, function() use ($con, $fd) {
                    $con->send($fd, '循环连接中' . "\n\n");
                });
            });
            $serv->on('receive', function($con, $fd, $from_id, $data) {
                if ($data == 'close') {
                    $con->close($fd);
                } else {
                    var_dump('接收到的数据' . $data);
                }
            });
            $serv->start();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function actionConnect()
    {
        try{
            $client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_TCP);

            $client->on("connect", function($cli) {
                $cli->send("已经连接上了\n");
            });

            $client->on("receive", function($cli, $data = ""){
                if($data === '') {
                    $cli->close();
                    echo "closed\n";
                } else {
                    echo "接收到的数据: $data\n";
                    sleep(1);
                    $cli->send("已经收到了\n");
                }
            });

            $client->on("error", function($cli){
                exit("error\n");
            });

            if ($clinet->connect('0.0.0.0', 9501)) {
                var_dump($clinet->getsockname());
                var_dump($clinet->isConnected());
                var_dump($client->send('再次确认连接，哈哈哈'));

            } else {
                echo "connect failed.";
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
