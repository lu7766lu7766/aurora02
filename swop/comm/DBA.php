<?php
//services.msc
namespace comm;

/**
 * @method DBA getAll($sql, $params, $option)
 * @method DBA exec($sql, $params, $option)
 * @method DBA query($sql, $params, $option)
 */
class DBA
{
    public $dbHost = ""; //"localhost";//125.227.84.247
    public $dbName = "";
    public $dbUser = "";
    public $dbPwd = "";
    public $dbPort = "";

    static public $reconnect = 0;
    public $conn;
    private $stmt;
//    static private $lib_path = "c:\\xampp\\apache\\bin\\httpd ";
//    static private $restart_command = "-k restart";
//    static private $login_id = "administrator";
//    static private $login_pwd = "22086229@tc";
//    static private $command_path = "c:\\xxx\\httpd.bat";
    private $begin_transaction = false;

    public function server_restart()
    {
        $login_id = getenv2('LOGIN_ID');
        $login_pwd = getenv2('LOGIN_PASSWORD');
        $restart_command = getenv2('COMMAND_PATH');
        $stop_command = getenv2('COMMAND_PATH2');
        if (DBA::$reconnect < 3) {

//            shell_exec('schtasks /Create /RU "' . $login_id . '" /RP "' . $login_pwd . '" /SC ONSTART /TR "' . $stop_command . '" /TN "temp"');
//            shell_exec('schtasks /Run /TN "temp"');
//            sleep(1);
//            shell_exec('schtasks /Delete /TN "temp" /F');
//            sleep(3);
            shell_exec('schtasks /Create /RU "' . $login_id . '" /RP "' . $login_pwd . '" /SC ONSTART /TR "' . $restart_command . '" /TN "temp"');
            shell_exec('schtasks /Run /TN "temp"');
            sleep(1);
            shell_exec('schtasks /Delete /TN "temp" /F');
            DBA::$reconnect++;
            //sleep(1);
            $this->connect();
            return $this->conn;
        } else {
            return false;
        }
    }

    public function connect()
    {
        $dbHost = $this->dbHost ? $this->dbHost : getenv2('DB_IP');
        $dbName = $this->dbName ? $this->dbName : getenv2('DB_NAME');
        $dbUser = $this->dbUser ? $this->dbUser : getenv2('DB_USER');
        $dbPwd = $this->dbPwd ? $this->dbPwd : getenv2('DB_PASSWORD');
        $dbPort = $this->dbPort ? $this->dbPort : getenv2('DB_PORT');
        $connectionInfo = ["Database" => $dbName, "UID" => $dbUser, "PWD" => $dbPwd, "CharacterSet" => "UTF-8"];
        $this->conn = sqlsrv_connect("$dbHost, $dbPort", $connectionInfo);
//        die(print_r([$this->conn, $dbHost, $dbPort, $connectionInfo]));
        if (!$this->conn) {
            if ($this->server_restart()) {
                return;
            }
            echo "資料庫連線失敗，請<a href='javascript:history.go(-1)'>回到上一頁</a>，並確定資料是否已異動<br />";
            die(print_r(sqlsrv_errors(), true));
        } else {
            DBA::$reconnect = 0;
            $this->begin_transaction();
        }
    }

    public function begin_transaction()
    {
        if ($this->conn && !$this->begin_transaction) {
            sqlsrv_begin_transaction($this->conn);
            $this->begin_transaction = true;
        }
    }

    public function __destruct()
    {
        $this->commit();
    }

    public function commit()
    {
        if ($this->begin_transaction) {
            sqlsrv_commit($this->conn);
        }
    }

    public function __call($function, $args)
    {
        $conn = $this->conn;
        $sql = $args[0] ?? "";
        $params = $args[1] ?? [];
        $options = $args[2] ?? [];
//妨礙debug
//        if(substr_count($sql,"?")!=count($a_paramter))
//            return false;
        switch ($function) {
            case "getAll":
                $stmt = sqlsrv_query($conn, $sql, $params, $options);
                if ($stmt === false) {
                    sqlsrv_rollback($conn);
                    var_dump($params);
                    die("illegality SQL! <br>{$this->mergeSQL($sql,$params)}<br>" . print_r(sqlsrv_errors(), true));
                }
                $rs = [];
                do {
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $rs[] = $row;
                    }
                } while (sqlsrv_next_result($stmt));
                return $rs;
                break;
            case "exec":
//                $this->begin_transaction();
                //執行兩次exec會當機，可能是這問題，目前解法是把所有update縮成一句，用分號隔開
                //加後面那句才可以用insert_id抓到id，如果是多句組成就不加這句
                if (strpos(strtolower($sql), 'insert') !== false && strpos(strtolower($sql), ';') === false) {
                    $sql .= "; SELECT SCOPE_IDENTITY() AS IDENTITY_COLUMN_NAME";
                }
                $stmt = sqlsrv_prepare($conn, $sql, $params, $options);
                if (!$stmt) {
                    die("illegality SQL! <br>{$this->mergeSQL($sql,$params)}<br>" . print_r(sqlsrv_errors(), true));
                } else {
                    if (sqlsrv_execute($stmt) === false) {
                        sqlsrv_rollback($conn);
                        die("SQL can't execute! <br>{$this->mergeSQL($sql,$params)}<br>" . print_r(sqlsrv_errors(),
                                true));
                    } else {
//                      sqlsrv_commit( $conn );
                        return $stmt;
                    }
                }
                break;
            case "query":
                $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
                $stmt = sqlsrv_query($conn, $sql, $params, $options);
                if (!$stmt) {
                    sqlsrv_rollback($conn);
                    die("illegality SQL! <br>{$this->mergeSQL($sql,$params)}<br>" . print_r(sqlsrv_errors(), true));
                }
                return $stmt;
                break;
            default:
                new Exception("don't find function!");
        }
    }

    function mergeSQL($sql, $params)
    {
        if (count($params)) {
            foreach ($params as $val) {
                $sql = $this->str_replace_first("?", $val, $sql);
            }
        }
        return $sql;
    }

    function str_replace_first($from, $to, $subject)
    {
        $from = '/' . preg_quote($from, '/') . '/';
        $to = is_null($to) ? "null" : "'" . $to . "'";
        return preg_replace($from, $to, $subject, 1);
    }

    public function getAllLimit()
    {
        $sql = func_get_arg(0) ?? "";
        $params = func_get_arg(1) ?? [];
        $offset = func_get_arg(2) ?? 1;
        $limit = func_get_arg(3) ?? 9999;
        //$orderBy = func_get_arg(4) ?? "";
        $limit = $offset + $limit - 1;
        $sql = "select * from (" . $sql . "
                  ) as mTable where rownum between {$offset} and {$limit} ";
//        echo $sql;
        return $this->getAll($sql, $params);
    }

    public function insert_id($stmt)
    {
        sqlsrv_next_result($stmt);
        sqlsrv_fetch($stmt);
        return sqlsrv_get_field($stmt, 0);
    }

    public function affected_rows($stmt)
    {
        if ($stmt) {
            return sqlsrv_rows_affected($stmt);
        }
    }

    public function num_rows($stmt)
    {
        if ($stmt) {
            return sqlsrv_num_rows($stmt);
        }
    }

    public function fetch_assoc($stmt)
    {
        if ($stmt) {
            return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        }
    }
}

?>
