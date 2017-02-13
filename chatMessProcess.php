<?php
header("Content-type: text/event-stream;charset=utf-8");
header("Cache-Control: no-cache");
//header("ACCESS-CONTROL-ALLOW-ORIGIN:*");
//header("ACCESS-CONTROL-ALLOW-METHOD:POST,GET,DELETE,PUT,OPTIONS");

session_start();

set_time_limit(0);

// 每隔1分钟去查一下数据库，如果有数据，就推送给前台
include_once("./DataPush.class.php");
include_once("./config.php");

DEFINED("USER_ID")  || DEFINE("USER_ID" ,isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : 1);

$DSN = _config("DSN");
$DataPush = new DataPush($DSN);

// 日志文件
$logOn = $DataPush->config['logSwitch'] == "on" ? true : false;
if($logOn) 
{
   $log_Path = __DIR__."/log_".date("Y-m-d").".txt";
   $fp = fopen($log_Path, "ab");
}

// 设置信息
if(isset($_POST['content']))
{
    $sql1 = "INSERT ".TABLE_DATA." (title,content,date,isRead,userId) values('chat','".$_POST['content']."','".date("Y-m-d H:i:s")."',0,".$_POST['userId'].")";
    if(!$DataPush->query($sql1)) 
    {
       if($logOn) fwrite($fp, DataPush::getError()."------".$DataPush->getLastSql()."------".date("Y-m-d H:i:s").PHP_EOL.PHP_EOL);
       die("data: ".DataPush::getError().PHP_EOL.PHP_EOL);
    }
    echo "succ";
    exit;
}
// 读完就更该信息的状态
elseif(isset($_POST['change']))
{ 
    $sql3 = "UPDATE ".TABLE_DATA." SET `isRead` = 1 WHERE id = ".$_POST['id'];
    if(!$DataPush->query($sql3))
    {
       if($logOn) fwrite($fp, DataPush::getError()."------".$DataPush->getLastSql()."------".date("Y-m-d H:i:s").PHP_EOL.PHP_EOL);
       die("data: ".DataPush::getError().PHP_EOL.PHP_EOL);
    }
    exit;
}
// 设置当前的用户
elseif(isset($_POST['objectId']))
{
    //DataPush::$userId = $_POST['userId'];
    $_SESSION['OBJECT_ID'] = $_POST['objectId'];
    exit;
}

$chatWith = $_SESSION['OBJECT_ID'];
$sql2 = "SELECT id,title,content FROM ".TABLE_DATA." WHERE DATE_FORMAT(date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') AND isRead = 0 AND userId = ".$chatWith." ORDER BY date DESC LIMIT 1";
$DataPush->setExecSql($sql2);
try
{
    $DataPush->Push(true);
}
catch(Exception $e)
{
    if($logOn) fwrite($fp, $e->getMessage().PHP_EOL.PHP_EOL);
    echo "data: ".$e->getMessage()."; last sql: ".$DataPush->getLastSql().PHP_EOL.PHP_EOL;
}



