<?php

/**
 * @method DBA getAll($sql, $params, $option)
 * @method DBA exec($sql, $params, $option)
 * @method DBA query($sql, $params, $option)
 */
class DBA2
{
    public $dbHost = "localhost";//125.227.84.247
    public $dbName = "AcAssistor";
    private $dbUser = "Assistor2008";
    private $dbPwd = "Assistor@2008R2";
    private $dbPort = 1276;
    private $stmt;
    private $begin_transaction = false;

    public function connect()
    {
        $dbHost = $this->dbHost;
        $dbName = $this->dbName;
        $dbUser = $this->dbUser;
        $dbPwd = $this->dbPwd;
        $dbPort = $this->dbPort;


        try {
            $this->conn = new PDO("sqlsrv:Server={$dbHost},{$dbPort};Database={$dbName}", $dbUser, $dbPwd);
            $this->begin_transaction();
        }
        catch (PDOException $e)
        {
            echo "Connection could not be established.<br />";
            die( print_r( $e->getMessage(), true));
        }
    }

    public function __construct()
    {
        //$this->connect();
    }

    public function __destruct()
    {
        $this->commit();
    }

    public function begin_transaction()
    {
        if($this->conn && !$this->begin_transaction){
            $this->conn->beginTransaction();
            $this->begin_transaction = true;
        }
    }

    public function commit()
    {
        if($this->begin_transaction)
        {
            $this->conn->commit();
        }
    }

    public function __call($function, $args)
    {
        $conn = $this->conn;

        $sql = $args[0] ?? "";
        $params = $args[1] ?? array();
        $options = $args[2] ?? array();

//妨礙debug
//        if(substr_count($sql,"?")!=count($a_paramter))
//            return false;

        switch ($function)
        {
            case "getAll":
                $stmt = $conn->prepare($sql);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute($params);
                if( $stmt === false ) {
                    $conn->rollback();
                    die( "illegality SQL! <br>{$sql}<br>".print_r( $conn->errorInfo(), true));
                }
                $rs = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $rs[] = $row;
                };
                return $rs;
                break;

            case "exec":
                $stmt = $conn->prepare($sql);
                if(!$stmt)
                {

                    die( "illegality SQL! <br>{$sql}<br>".print_r($params).print_r( $conn->errorInfo(), true));
                }
                else
                {
                    if($stmt->execute($params)===false)
                    {
                        $conn->rollback();
                        die( "SQL can't execute! <br>{$sql}<br>".print_r($params).print_r( $conn->errorInfo(), true));
                    }
                    else
                    {
//                      sqlsrv_commit( $conn );
                        return $stmt;
                    }
                }

                break;

            case "query":

                $stmt = $conn->prepare($sql);

                if(!$stmt)
                {
                    die( "illegality SQL! <br>{$sql}<br>".print_r($params).print_r( $conn->errorInfo(), true));
                }
                else
                {
                    if($stmt->execute($params)===false)
                    {
                        $conn->rollback();
                        die( "SQL can't execute! <br>{$sql}<br>".print_r($params).print_r( $conn->errorInfo(), true));
                    }
                    else
                    {
                        return $stmt;
                    }
                }

                break;

            default:

                new Exception("don't find function!");
        }
    }

    public function getAllLimit()
    {
        $sql = func_get_arg(0) ?? "";
        $params = func_get_arg(1) ?? array();
        $offset = func_get_arg(2) ?? 1;
        $limit = func_get_arg(3) ?? 9999;
        //$orderBy = func_get_arg(4) ?? "";
        $limit = $offset+$limit-1;

        $sql = "select * from (".$sql."
                  ) as mTable where rownum between {$offset} and {$limit} ";
//        echo $sql;
        return $this->getAll($sql,$params);

    }

    public function insert_id($stmt)
    {
        //return $this->conn->lastInsertId();
        return $stmt->lastInsertId();
    }

    public function affected_rows($stmt)
    {
        if($stmt)
            return $stmt->rowCount();
    }

    public function num_rows($stmt)
    {
        if($stmt)
            return $stmt->rowCount();
    }

    public function fetch_assoc($stmt)
    {
        if($stmt)
            return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>
