<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
/**
* Test Console Application
*/
class TestController extends Controller
{
    private $_serv;
    private function _prepare()
    {
        $this->_serv = new \Swoole\Server('127.0.0.1', 9501);
        $this->_serv->set([
            'worker_num' => 1,
        ]);
        $this->_serv->on('Start', [$this, 'onStart']);
        $this->_serv->on('Receive', [$this, 'onReceive']);
        $this->_serv->on('Close', [$this, 'onClose']);
    }
    public function actionStart ()
    {
        $this->_prepare();
        $this->_serv->start();
    }
    public function onStart($serv)
    {
        yii::warning("This is a warning message.");
    }
    public function onReceive($serv, $fd, $fromId, $data)
    {
    }
    public function onClose($serv)
    {
    }

    public function actionPool()
    {
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', 9501, -1))
        {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        $client->send("hello world\n");
        echo $client->recv();
        $client->close();
    }
}
