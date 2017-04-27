<?php

//正式环境 需要屏蔽该行代码
error_reporting(E_WARNING | E_ERROR);

/*
获取消息成功接口
{init: 1, name: "Jamai", date: "2017-04-26 00:00:00", detail: "你好"}
获取消息失败接口
{init:0}

接受消息成功接口
{init:1}
接受消息失败接口
{init:0}
*/

if ( empty($_POST) ) {
    exit();
}

session_start();
$params = $_POST;

//客户端获取消息
if ( $params['action'] == "get" ) {

    include_once './core/Database.inc.php';
    $config = include_once './conf.php';

    if ( !isset($params['chatname']) || !$params['chatname'] ) {
        echo "{errCode: 100}";
        exit;
    }

    if ( !isset($_SESSION['username']) ) {
        $sql = 'SELECT user_id, username FROM `user` WHERE username = "' . $params['chatname'] . '"';
        $user = Database::getInstance($config)->query($sql, 'Row');

        if ( !$user['user_id'] ) {
            echo "{errCode: 100}";
            exit;
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
    }

    //要获取的消息ID
    if ( !isset($_SESSION['cid']) ) {
        $_SESSION['cid'] = 0;
    }

    $id = $params["id"] == 0 ? $_SESSION['cid'] - 1 : $params['id'];

    //获取最后一条消息显示
    $sql = 'SELECT * FROM chat c LEFT JOIN user u ON c.sender = u.user_id WHERE c.id = ' . $id;
    $msg = Database::getInstance($config)->query($sql, 'Row');

    //对消息进行处理
    if ( !empty($msg) ) {
        //获取成功
        $result = [
            'init' => 1,
            'nid' => $_SESSION['cid'],
            'name' => $msg['username'],
            'date' => date('Y-m-d H:i'),
            'detail' => $msg['message']
        ];

        echo json_encode($result);
    }
    else {
        //获取失败
        echo "{init:0}";
    }
}
else if ( $params['action'] == "send" ) {

    include_once './core/Database.inc.php';
    $config = include_once './conf.php';

    $name = htmlspecialchars($params['chatname']);
    $msg = htmlspecialchars($params['chatmsg']);

    $sql = 'SELECT `user_id`, `username` FROM `user` WHERE username = "' . $params['chatname'] . '"';
    $user = Database::getInstance($config)->query($sql, 'Row');

    //当用户不存在是  则自动增加用户信息 如果用户存在是 直接获取ID
    $userID = 0;
    if ( !$user ) {
//        $sql = "INSERT INTO user(`username`, `created`) VALUE ('" . $params['chatname'] . "', {time()})";
//        $user = Database::getInstance($config)->query($sql, 'Row');

        $user = Database::getInstance($config)->insert('user', [
            'username' => $params['chatname'],
            'created' => time(),
        ]);

        if ( $user ) {
            $userID = Database::getInstance($config)->lastInsertId();
        }
    }
    else {
        $userID = $user['user_id'];
    }

    //存储信息
    $msg = Database::getInstance($config)->insert('chat', [
        'sender' => $userID,
        'message' => $msg,
        'created' => time(),
    ]);

    //输出
    if ( $msg ) {

        //记录最新的消息ID
        $_SESSION['cid'] = (int) Database::getInstance($config)->lastInsertId() + 1;

        echo "{init:1}";
        exit;
    }
    else {
        echo "{init:0}";
        exit;
    }
}
