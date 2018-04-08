<?php
    $client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);

    $client->on("connect", function($cli) {
        $cli->send("已经连接上了\n");
    });

    $client->on("receive", function($cli, $data = ""){
	echo $data;
        if($data == 'close') {
	    echo '23456\n';
            $cli->close();
        } else {
            echo "接收到的数据: $data\n";
            sleep(1);
            $cli->send("已经收到了\n");
        }
    });

    $client->on("error", function($cli){
        exit("error\n");
    });
    
    $client->on("close", function($cli){
        echo "close22\n";
    });

    if ($client->connect('0.0.0.0', 9501)) {
        
    } else {
        echo '连接失败';
    }
?>
