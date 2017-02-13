<?php
/*
 * CONFIG
 */

DEFINED("TABLE_DATA") || DEFINE("TABLE_DATA" ,'data');
DEFINED("TABLE_USER") || DEFINE("TABLE_USER" ,'user');
DEFINED("SEND_FAIL")  || DEFINE("SEND_FAIL" ,'MESSAGE SEND FAIL');

$config = array
          (
              "DSN"         => "localhost|root||data|2",
              "INPUT_WRONG" => "信息输入格式有误",
              "USER_NOT_EXISTS" => "用户不存在",
              "USER_EXISTS" => "用户已存在",
              "LOGIN_SUCC"  => "登陆成功",
              "SIGNUP_SUCC"  => "注册成功",
              "SIGNUP_FAIL"  => "注册成功",
              "SIGNUP_FATAL"  => "非法注册",
              "MULTI_LOGIN"  => "您已在别处登陆",
              "ACTION_SUCC"  => "操作成功",
              "SEARCH_FAIL"  => "抱歉,没有查到该用户",
              "LOGIN_FAIL"  => "用户和密码不匹配",
              "SIGNUP_REPEATE"  => "1小时内不允许重复注册",
              "ALLOW_HOST" => array(
                                     "localhost",
                                     "www.guitoo.cc",
                                     "192.168.1.108",
                                   ),
          );

function _config($key)
{
    global $config;
    return $config[$key];
}

function _getIp()
{
    if(getenv("HTTP_X_FORWARDED_FOR") && isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $ips);
        if($pos !== false) unset($ips[$pos]);
        $ip = trim($ips[0]);
    }
    elseif(getenv("HTTP_CLIENT_IP") && isset($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = getenv("HTTP_CLIENT_IP");
    }
    elseif(getenv("REMOTE_ADDR") && isset($_SERVER['REMOTE_ADDR']))
    {
        $ip = getenv("REMOTE_ADDR");
    }
    return $ip;
}

function _afterActionSucc($dataObj, $execSql, $userInfo)
{
    if(!empty($userInfo) && is_array($userInfo) && $dataObj && $execSql)
    {
        if(isset($userInfo['id']))    $_SESSION['userId'] = $userInfo['id'];
        if(isset($userInfo['uName'])) $_SESSION['uName']  = $userInfo['uName'];
        $dataObj->setExecSql(sprintf($execSql, 1, $userInfo['id']));
        $dataObj->query('');
    }
}

