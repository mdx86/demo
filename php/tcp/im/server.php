<?php
error_reporting(E_ERROR);

$ip = '0.0.0.0';    //绑定的IP
$port = 8585;  //监听的端口号

$nickName = '李四';

//收放数据格式约定为json格式，格式为：
$format = [
    'name' => '昵称',
    'time' => '发送时间戳',
    'message' => '消息内容',
];

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//绑定
socket_bind($socket, $ip, $port);
//监听
socket_listen($socket);

echo "--------------------------\n";
echo "我的昵称：$nickName\n绑定IP：{$ip}，端口：$port\n";
echo "--------------------------\n";

$exiting = false;

while (true) {
    if ($exiting) {
        break;
    }

    echo "正在监听...\n";
    $accept = socket_accept($socket);
    if ($accept === false) {
        break;
    }

    socket_getpeername($accept, $clientIP, $clientPort);

    echo "--------------------------\n";
    echo "已建立连接...\n";
    echo "客户端IP：$clientIP,端口：$clientPort\n";
    echo "--------------------------\n";

    while (true) {
        //接收客户端数据
        //echo "正在等待回复...\n";
        $json = socket_read($accept, 1024);
        if (!$json) {
            echo "连接已断开...\n";
            break;
        }
        //解析数据
        $data = json_decode($json, true);
        $name = $data['name'];
        $time = $data['time'];
        $date = date('Y-m-d H:i:s', $time);
        $message = $data['message'];
        $output = "$name $date 说：$message\n";
        echo $output;

        //向客户端发送消息
        echo "我：";
        $message = fgets(STDIN);
        $message = trim($message);
        if ($message == 'exit') {
            $exiting = true;
            break;
        }

        $data = [
            'name' => $nickName,
            'message' => $message,
            'time' => time(),
        ];
        $json = json_encode($data);
        $length = strlen($json);

        $s = socket_write($accept, $json, $length);
        if ($s === false) {
            break;
        }
    }

}

socket_close($socket);
echo "会话已结束\n";