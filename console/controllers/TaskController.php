<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
//use common\Taskrun;
/**
* Test Console Application
*/
class TaskController extends Controller
{
    private $_serv;
    private $_run;
    private $_pool;
    public $enableCsrfValidation = false;
    /**
     * init
     */
    public function prepare()
    {
        $this->_serv = new \Swoole\Server("127.0.0.1", 9501);
        $this->_serv->set([
            'worker_num' => 10,
            'daemonize' => false,
            'log_file' => __DIR__ . '/runtime/server.log',
            'task_worker_num' => 2,
            'max_request' => 5000,
            'task_max_request' => 5000,
            // 'open_eof_check' => true, //打开EOF检测
            // 'package_eof' => "\r\n", //设置EOF
            // 'open_eof_split' => true, // 自动分包
        ]);
        $this->_serv->on('Connect', [$this, 'onConnect']);
        $this->_serv->on('Receive', [$this, 'onReceive']);
        // $this->_serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->_serv->on('Task', [$this, 'onTask']);
        $this->_serv->on('Finish', [$this, 'onFinish']);
        $this->_serv->on('Close', [$this, 'onClose']);
    }

    public function actionPool()
    {
        $db = new \swoole_mysql;
        $server = array(
            'host'      => '127.0.0.1',
            'user'      => 'root',
            'password'  => 'mysql729082',
            'database'  => 'test',
            'charset'   => 'utf8', //指定字符集
            'timeout'   => 2,  // 可选：连接超时时间（非查询超时时间），默认为SW_MYSQL_CONNECT_TIMEOUT（1.0）
        );
        $db->connect($server, function ($db, $r) {
            if ($r === false) {
                var_dump($db->connect_errno, $db->connect_error);
                die;
            }
            $db->query("insert into users (name) value (22)", function(swoole_mysql $db, $r) {
                if ($r === false)
                {
                    var_dump($db->error, $db->errno);
                }
                elseif ($r === true )
                {
                    echo $db->affected_rows . PHP_EOL;
                }
                echo $r . PHP_EOL;
                $db->close();
            });
        });
        // $pool = new \Swoole\MySQL([
        //     'host'      => '127.0.0.1',
        //     'user'      => 'root',
        //     'password'  => 'mysql729082',
        //     'database'  => 'test'
        // ]);
        // for ($i = 0; $i < 1000; $i++) {
        //     $dat = $pool->query("insert into users (name) value ($i)", function(swoole_mysql $mysqli, $result) {
        //         var_dump($result->fetch_all());
        //     });
        //     echo $dat;die;
        // }
    }

    public function onConnect($serv, $fd, $fromId)
    {
        echo $fd . '连接上了';
        $serv->send($fd, "成功建立连接");
    }
    // public function onWorkerStart($serv, $workerId)
    // {
    //     $this->_run = new Taskrun;
    // }
    public function onReceive($serv, $fd, $fromId, $data)
    {
        // $data = $this->unpack($data);

        // $this->_run->receive($serv, $fd, $fromId, $data);

        // // 投递一个任务到task进程中
        // if (!empty($data['event'])) {
        //     $serv->task(array_merge($data , ['fd' => $fd]));
        // }
        for ($i = 0; $i < 10; $i++) {
            $result = $serv->task($i);
            echo $result . PHP_EOL;
        }

    }
    public function onTask($serv, $taskId, $fromId, $data)
    {
        static $link = null;
        if ($link == null) {
            // $link = new \Swoole\MySQL([
            //     'host'      => '127.0.0.1',
            //     'user'      => 'root',
            //     'password'  => 'mysql729082',
            //     'database'  => 'test'
            // ]);
            $link = mysqli_connect("127.0.0.1", "root", "mysql729082", "test");
            if (!$link) {
                $link = null;
                return;
            }
        }
        echo $data . '-' . $taskId . "\n";
        $result = $link->query("insert into users (name) value (" . $data . '@' . $taskId . ")");
        if (!$result) {
            echo "ER:" . mysqli_error($link);
            return;
        }
        echo "OK:" . serialize($result) . "\n";
        // $data = $result->fetch_all(MYSQLI_ASSOC);
        // echo "OK:" . serialize($data) . "\n";
        // $this->_run->task($serv, $taskId, $fromId, $data);
    }
    public function onFinish($serv, $taskId, $data)
    {
        // $this->_run->finish($serv, $taskId, $data);
    }
    public function onClose($serv, $fd, $fromId)
    {

    }

    /**
     * 对数据包单独处理，数据包经过`json_decode`处理之后，只能是数组
     * @param $data
     * @return bool|mixed
     */
    public function unpack($data)
    {
        $data = str_replace("\r\n", '', $data);
        if (!$data) {
            return false;
        }
        $data = json_decode($data, true);
        if (!$data || !is_array($data)) {
            return false;
        }
        return $data;
    }

    public function actionStart()
    {
    	try {
    	    $this->prepare();
            $this->_serv->start();
    	} catch (\Exception $e) {
    	    echo $e->getMessage();
    	}
    }
}
