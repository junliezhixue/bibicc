<?php
namespace common;
use Yii;
//use yii\web\Controller;
/**
* Test Console Application
*/
class Taskclient
{
    private $client;

    const EVENT_TYPE_SEND_MAIL = 'send-mail';

    public function __construct()
    {
        $this->client = new \Swoole\Client(SWOOLE_SOCK_TCP);
        if (!$this->client->connect('127.0.0.1', 9501)) {
            $msg = 'swoole client connect failed.';
            throw new \Exception("Error: {$msg}.");
        }
    }
    /**
     * @param $data Array
     * send data
     */
    public function sendData ($data = '')
    {
        $data = [];
        $data['from']       = '1412279986@qq.com';
        $data['to']         = '2273449524@qq.com';
        $data['subject']    = '历程';
        $data['content']    = '啦啦啦啦啦啦啦啦啦啦啦啦';
        $data['event']      = self::EVENT_TYPE_SEND_MAIL;
        $data = $this->togetherDataByEof($data);
        $ret =  $this->client->send($data);
	return $ret;
    }

    /**
     * 数据末尾拼接EOF标记
     * @param Array $data 要处理的数据
     * @return String json_encode($data) . EOF
     */
    public function togetherDataByEof($data)
    {
        if (!is_array($data)) {
            return false;
        }
        return json_encode($data) . "\r\n";
    }
}

