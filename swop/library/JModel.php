<?php
//require_once "swop/library/dba.php";
//$dba=new dba();
use comm\DB;
use comm\Http;

class JModel extends Model
{
    public function __construct($base)
    {
        parent::__construct($base);
        $this->user = $this->session["login"];
        $this->subEmp = $this->session["sub_emp"];
        $this->choice = $this->session["choice"];//被選中的使用者
        $this->permission = $this->session["permission"];
    }

    public function delTreePrefix($dir, $prefix = "")
    {

        $files = glob($dir . $prefix . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (substr($file, -1) == '/') {
                delTree($file);
            } else {
                unlink($file);
            }
        }
    }

    public function getSubEmp($userId, $class = 1)
    {
        $dba = $this->dba;
        $a_rs = [];
        if (!is_string($userId)) {
            return $a_rs;
        }
        $sql = "select UserID,UserName,UserGroup,MenuList,ParentID,PermissionControl from SysUser
                      where ParentID=?";
        $result = $dba->getAll($sql, [$userId]);
        $len = count($result);
        //echo $len."^^";
        $i = 0;
        while ($i < $len) {
            $a_tmp = $result[$i++];
            $a_tmp["class"] = $class;
            $a_rs[] = $a_tmp;
            $a_rs = array_merge($a_rs, $this->getSubEmp($a_tmp["UserID"], $class + 1));
        }
//        echo count($a_rs)."^^";
//        print_r($a_rs);die();
        return $a_rs;
    }

    public function readUploadList($field_name = "list")
    {
        if (file_exists($_FILES[$field_name]['error'])) {
            return -1;
        }
        if (!file_exists($_FILES[$field_name]['tmp_name'])) {
            return false;
        }
        $myfile = fopen($_FILES[$field_name]['tmp_name'], "r") or die("Unable to open file!");
        $content = fread($myfile, filesize($_FILES[$field_name]['tmp_name']));
        fclose($myfile);
        $result = $this->splitNumberList($content);
        return $result;
    }

    public function splitNumberList($content)
    {
        $content = preg_replace('/[^\d,]/', '', preg_replace('/\s/', ',', trim($content)));
        while (substr_count($content, ',,')) {
            $content = strtr($content, [",," => ","]);
        }
        return explode(',', $content);
    }

    public function convertQuotes($str)
    {
        if (strpos($str, "\"") !== 0) {
            return preg_replace("/\"/", "'", $str);
        } else {
//            return preg_replace("/\"/g", "'", $str);
        }
        return $str;
    }

    public function chunkInsertDB($table, $body, $chunk_limit = 100)
    {
        // 輸入筆數
        $len = count($body);
        $i = 0;
        $execBody = [];
        while ($i < $len) {
            $execBody[] = $body[$i++];
            if ($i % $chunk_limit == 0) {
                DB::table($table)->insert($execBody)->exec();
                $execBody = [];
            }
        }
        if (count($execBody)) {
            DB::table($table)->insert($execBody)->exec();
        }
    }

    public function chunkInsertDB2($table, $body, $chunk_limit = 100)
    {
        // 輸入筆數
        $dba = new \comm\DBA();
        $dba->connect();
        $sql = '';
        $params = [];
        if (count($body)) {
            $a_fields = array_keys($body[0]);
            $fields = join($a_fields, ',');
            $values = join(array_fill(0, count($a_fields), '?'), ',');
            foreach ($body as $i => $data) {
                $sql .= "insert into {$table} ({$fields}) values ({$values});";
                $params = array_merge($params, array_values($data));
                if ($i % $chunk_limit == 50) {
                    $dba->exec($sql, $params);
                    $sql = '';
                    $params = [];
                }
            }
            if (count($params)) {
                $dba->exec($sql, $params);
            }
        }
    }
}

?>
