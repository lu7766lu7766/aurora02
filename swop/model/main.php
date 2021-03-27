<?php
//require_once "swop/library/dba.php";
//$dba=new dba();
class Main_Model extends JModel
{
    public function main()
    {
        //$dba = $this->dba;
        return $this;
    }

    public function login_in()
    {
        $dba = $this->dba;
        $username = $this->username;
        if (empty($username)) {
            return false;
        }
        $password = $this->password;
        $password = \lib\Hash::encode($password);
        $sql = "select UserID,UserName,UserGroup,MenuList,PermissionControl,CanSwitchExtension from SysUser
                      where (UserID=? or UserID2=?) AND
                      UserPassword=?";
        $result = $dba->getAll($sql, [$username, $username, $password]);
        if (count($result)) {
            $this->session["login"] = $result[0];
            $this->session["choicer"] = $result[0];
            $this->session["choice"] = $this->session["login"]["UserID"];
            $this->session["isRoot"] = $this->session["choice"] == "root";
            $this->session["permission"] = $this->session["login"]["MenuList"];//$this->session["login"]["UserGroup"];
            $this->session["sub_emp"] = $this->getSubEmp($this->session["login"]["UserID"]);
            //uasort($this->session["sub_emp"],function($a,$b){ return $a["class"] <=> $b["class"];});//照層級
            uasort($this->session["sub_emp"], function ($a, $b) {
                return $a["UserID"] <=> $b["UserID"];
            });//ＩＤ
            $this->session["current_sub_emp"] = EmpHelper::getCurrentSubEmp($this->session["sub_emp"],
                $this->session["choice"]);
            $this->session["permission_control"] = $this->session["login"]["PermissionControl"];
//            uasort($_SESSION["sub_emp"],"my_sort"
////                function($a,$b){
////                return  $a["class"]<=> $b["class"];
//////                return $a["class"] == $b["class"]? 0:
//////                    $a["class"] < $b["class"]? -1: 1;
////            }
//            );
            return true;
        }
        return false;
    }
}

?>
