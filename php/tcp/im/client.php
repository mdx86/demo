<?php
error_reporting(E_ERROR);

echo "请输入服务器IP（默认为：127.0.0.1）：";
$ip = fgets(STDIN);
$ip = trim($ip);
$ip = $ip ?: '127.0.0.1';
$port = 8585;

$nickName = '张三';

$st = "socket send message";
$length = strlen($st);
//创建tcp套接字
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

echo "正在连接服务器：$ip:$port\n";
//连接tcp
$flag = socket_connect($socket, $ip, $port);
if ($flag === false) {
    echo "无法连接到服务器：{$ip}:{$port}，请稍后重试...\n";
    return;
}

echo "--------------------------\n";
echo "我的昵称：$nickName\n服务器IP：{$ip}，端口：$port\n";
echo "--------------------------\n";


echo "已建立连接...\n";

while (true) {
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

    //向打开的套集字写入数据（发送数据）
    $s = socket_write($socket, $json, $length);
    if ($s === false) {
        echo "连接已断开...\n";
        break;
    }

    //echo "正在等待回复...\n";
    //从套接字中获取服务器发送来的数据
    $json = socket_read($socket, 1024);
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
}

//关闭连接
socket_close($socket);
echo "会话已结束\n";