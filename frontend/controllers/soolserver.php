<?php
    $serv = new swoole_server('0.0.0.0', 9501);
    $serv->set([
            'worker_num'                => 1,
            'max_request'               => 30,
            'backlog'                   => 20,
            'log_file'                  => '/data/log/swoole.log',
            'heartbeat_check_interval'  => 30,
            'heartbeat_idle_time'       => 60
        ]);
    $serv->on('connect', function($con, $fd) {
	$timer = 0;
        $con->tick(2000, function() use ($con, $fd, &$timer) {
	    if ($timer < 3) {
		$timer += 1;
            	$con->send($fd, '循环连接中' . "\n\n");
	    } else {
	    	$con->send($fd, 'close');
	    }
        });
    });
    $serv->on('receive', function($con, $fd, $from_id, $data) {
        if ($data == 'close') {
            $con->close($fd);
        } else {
            echo('接收到的数据' . $data . "\n\n");
        }
    });
    $serv->start();
?>
