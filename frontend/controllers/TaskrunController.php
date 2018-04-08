<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\frontend\Taskclient;

class TaskrunControllers extends Controller
{
    public function receive($serv, $fd, $fromId, $data)
    {

    }
    public function task($serv, $taskId, $fromId, $data)
    {
        try {
            switch ($data['event']) {
                case Taskclient::EVENT_TYPE_SEND_MAIL:
                    $result = Yii::$app->mailer->compose()
                        ->setFrom($data['from'])
                        ->setTo($data['to'])
                        ->setSubject($data['subject'])
                        ->setTextBody($data['content'])
                        ->send();
                    $result = ($result == 'success') ? true : false;
                    break;
                default:
                    break;
            }
            $serv->send($data['fd'], '发送成功');
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('task exception :' . $e->getMessage());
        }
    }
    public function finish($serv, $taskId, $data)
    {
        return true;
    }
}