<?php
/**
 * DataPush Class
 * @param ssp
 * @lastDate 2016-09-26 14:26
 */
class DataPush
{
public $sleeptime;
private $host;
private $user;
private $pass;
private $dbname;
private $mysqli;
private $currentExecSql;
public static $isError;
public static $error;
protected $SQLLogs;
public static $lastInsertId;
//public static $userId;

public $config = array
(
    "logSwitch" => "on",
);
// $host = '127.0.0.1', $user = 'root', $pass = '', $dbname = 'data', $sleeptime = 2 ,$connect = true
public function __construct($DSN)
{
   list($host, $user, $pass, $dbname,$sleeptime) = explode("|", $DSN);
   $this->host = $host;
   $this->user = $user;
   $this->pass = '';
   $this->dbname    = $dbname;
   $this->sleeptime = $sleeptime;
   $this->SQLLogs   = array();
   $this->mysqli = new mysqli($host, $user, $pass, $dbname);

   if($this->mysqli->connect_errno) die("data: CONNECT DATABASE FAILD!".PHP_EOL.PHP_EOL);
   if(!$this->mysqli->query("SET NAMES UTF8")) die("data: SET NAMES FAILD!".PHP_EOL.PHP_EOL);
}

public function setExecSql($sql = '')
{
    if($sql) $this->currentExecSql = $sql;
}

public function setSqlLog($sql = '')
{
    $this->SQLLogs[] = $sql;
}

public function getLastSql()
{
    array_map("trim", $this->SQLLogs);
    return reset($this->SQLLogs);
}

public function Push($hasLongConnection = false)
{
    $sql = !empty($this->currentExecSql) ? $this->currentExecSql : '';
    if($hasLongConnection) 
    {
        echo $this->exec($sql);
        exit;
    }
    
    while(true)
    {   
        $return = $this->exec($sql);echo $return;
        if(strpos($return, "NO MESSAGE") === false) break;
        @ob_flush();@flush();
        sleep($this->sleeptime);
    }
    return;
}

public function exec($sql, $return = false)
{
    if($result = $this->mysqli->query(get_magic_quotes_gpc() ? $sql : stripslashes($sql)))
    {
        $this->setSqlLog($sql);
        $return = $result->fetch_all(MYSQLI_ASSOC);
        if(!empty($return))
        {
            return "data: ".json_encode(array('id' => $return[0]['id'], 'title' => $return[0]['title'], 'content' => mb_convert_encoding($return[0]['content'], "utf-8"))).PHP_EOL.PHP_EOL;
            $result->close();
            $this->mysqli->close();
        }
        return "data: NO MESSAGE!".PHP_EOL.PHP_EOL;
    }
}

public static function getError()
{
    return self::$error;
}

public function getLastId()
{
    return self::$lastInsertId;
}

public function query($sql = '', $fetch = false)
{

    $sql = (!$sql) ? $this->currentExecSql : $sql;
    if($result = $this->mysqli->query(get_magic_quotes_gpc() ? $sql : stripslashes($sql)))
    {
        $this->setSqlLog($sql);
        if(is_object($result))
        {
            $return = $result->fetch_all(MYSQLI_ASSOC);
            // if(!empty($return)) {
                $result->close();
                if($fetch) return current($return);
                else return $return;
            //}
        }
        if($this->mysqli->insert_id) self::$lastInsertId = $this->mysqli->insert_id;
        return true;
    }
    self::$isError = true;self::$error = $this->mysqli->error;
    return false;
}

}