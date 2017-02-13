<?php
header('Content-type:text/html;charset=utf-8');

include_once("./DataPush.class.php");
include_once("./config.php");

session_start();

$DSN = _config("DSN");
$DataPush = new DataPush($DSN);

// 日志文件
$logOn = $DataPush->config['logSwitch'] == "on" ? true : false;
if($logOn) 
{
   $log_Path = __DIR__."/log_".date("Y-m-d").".txt";
   $fp = fopen($log_Path, "ab");
}

// 数据处理
$action = $_GET['action'];
// checkUser
$checkUserSql = "SELECT count(*) as count FROM ".TABLE_USER." WHERE binary uName = '%s'";
// newUser
$newUserSql   = "INSERT INTO ".TABLE_USER." values(NULL, '%s', '%s', 0)";
// change
$changeSql    = "UPDATE ".TABLE_USER." SET isLogin = %d WHERE id = %d";
// search
$searchSql    = "SELECT * FROM ".TABLE_USER." WHERE uName = '%s' AND uName != '%s' LIMIT 1";

if($_POST || getenv('REQUEST_METHOD') == "POST")
{
    if(in_array($action, array('in', 'up')))
    {
        $data   = $_POST['data'];
        list($uNameStr, $uPassStr) = explode("&", $_POST['data']);
        $uName = urldecode(explode("=", $uNameStr)[1]);
        $uPass = urldecode(explode("=", $uPassStr)[1]);
        // 昵称4-12，密码4-16不能有中文字符
        if(!preg_match("/^[\w\+\=\-\*\^\%\$\#\@\~]{4,16}$/", $uPass) || !in_array(mb_strlen($uName, 'UTF-8'), array(4,5,6,7,8,9,10,11,12)) || preg_match("/^[\'\"\/\`\?\!\~\{\}\]]+$/", $uName))
        {
            echo json_encode(array('result' => 'fail', 'message' => _config("INPUT_WRONG"), 'errorCode' => sprintf("%'04s", 2)));exit;
        }
        else
        {
            // sign in
            if($action == 'in')
            {
                // 检测用户是否已经存在
                $DataPush->setExecSql(sprintf($checkUserSql, $uName));
                $result = $DataPush->query('', true);

                if(!$result['count']) 
                {
                   echo json_encode(array('result' => 'fail', 'message' => _config("USER_NOT_EXISTS"), 'errorCode' => sprintf("%'04s", 3)));
                   exit;
                }

                // 确认密码
                $checkPassSql = "SELECT * FROM ".TABLE_USER." WHERE uName = '".$uName."'";
                $DataPush->setExecSql($checkPassSql);
                $userInfo = $DataPush->query('', true);

                if($userInfo['uPass'] === md5(md5($uPass.substr($uName, 0 ,4)))) 
                {
                   // 不允许多处登陆
                   if($userInfo['isLogin'] == 1) {
                      $_SESSION['userInfo'] = $userInfo;
                      echo json_encode(array('result' => 'fail', 'message' => _config("MULTI_LOGIN"), 'errorCode' => sprintf("%'04s", 9)));
                      exit;
                    }
                   _afterActionSucc($DataPush, $changeSql, $userInfo);

                   echo json_encode(array("result" => 'success', "message" => _config("LOGIN_SUCC"), 'userId' => $userInfo['id']));
                   exit;
                }

                echo json_encode(array("result" => 'fail', "message" => _config("LOGIN_FAIL"), 'errorCode' => sprintf("%'04s", 12)));
                exit;
            }
            elseif($action == 'up')
            {
                //限制跨域注册
                if(getenv('HTTP_ORIGIN') && isset($_SERVER['HTTP_ORIGIN']) && getenv('HTTP_REFERER') && isset($_SERVER['HTTP_REFERER']))
                {
                   if(!in_array(str_replace(array("http://", "https://"), '',getenv('HTTP_ORIGIN')), _config('ALLOW_HOST')) && strpos(getenv('HTTP_REFERER'), "onlinechat.htm") === false)
                   {
                      echo json_encode(array("result" => 'fail', "message" => _config("SIGNUP_FATAL"), 'errorCode' => sprintf("%'04s", 8)));
                      exit;
                   }
                }
                //记录注册时间
                $ip = _getIp();
                if(isset($_SESSION[$ip."_LastSignUpTime"]))
                {
                    if(time() - strtotime($_SESSION[$ip."_LastSignUpTime"]) < 3600) { echo json_encode(array("result" => 'fail', "message" => _config("SIGNUP_REPEATE"), 'errorCode' => sprintf("%'04s", 2)));exit; } 
                }
                // check user exists
                $DataPush->setExecSql(sprintf($checkUserSql, $uName));
                $result = $DataPush->query('', true);
                if($result['count']) { echo json_encode(array("result" => 'fail', "message" => _config("USER_EXISTS"), 'errorCode' => sprintf("%'04s", 4)));exit; }

                // insert
                $DataPush->setExecSql(sprintf($newUserSql, $uName, md5(md5($uPass.substr($uName, 0 ,4)))));
                $result = $DataPush->query('', true);
                if($result)
                {
                   $insertId = $DataPush->getLastId();
                   $_SESSION[$ip."_LastSignUpTime"] = date('Y-m-d H:i:s', time());

                   $userInfo = array('id' => $insertId, 'uName' => $uName);
                   _afterActionSucc($DataPush, $changeSql, $userInfo);

                   echo json_encode(array("result" => 'success', "message" => _config("SIGNUP_SUCC"), "userId" => $insertId));
                   exit;
                }
                if($DataPush->getError()) {echo json_encode(array("result" => 'fail', "message" => _config('SIGNUP_FAIL'), 'errorCode' => sprintf("%'04s", 7)));exit;}
            }
        }
    }
    // 搜索
    if($action == 'search')
    {
        $searchName = $_POST['objectName'];
        if(trim($searchName) !== '')
        {
            $DataPush->setExecSql(sprintf($searchSql, $searchName, $_SESSION['uName']));
            $result = $DataPush->query('', true);
            if($result && !empty($result) && !$DataPush->getError())
            {
                $returnArray = array();
                $returnArray['result']     = 'success';
                $returnArray['message']    = _config("ACTION_SUCC");
                $returnArray['objectId']   = $result['id'];
                $returnArray['objectName'] = $result['uName'];
                echo json_encode($returnArray);
                exit;
            }
            echo json_encode(array('result' => 'fail', 'message' => _config("SEARCH_FAIL"), 'errorCode' => sprintf("%'04s", 11)));exit;
        }
    }
    elseif($action == 'resignin')
    {
        $userInfo = $_SESSION['userInfo'];
        _afterActionSucc($DataPush, $changeSql, $userInfo);
        echo json_encode(array('result' => 'success'));
        exit;
    }
}

// 注销
if($action == 'out') 
{
   $DataPush->setExecSql(sprintf($changeSql, 0, $_SESSION['userId']));
   $DataPush->query('');
   session_unset();
   session_destroy();
   $REFERER = !empty(getenv("HTTP_REFERER")) ? getenv("HTTP_REFERER") : __DIR__."/onlineChat.html";
   header("Location: ".$REFERER);
}
