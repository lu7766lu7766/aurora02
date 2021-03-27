<?php
//require_once "swop/library/dba.php";
//$dba=new dba();
class Index_Model extends JModel
{
    public function index()
    {
        return $this;
    }

    public function update_pwd()
    {
        $id = $this->userId;
        $pwd = $this->password;
        if (!empty($pwd)) {
            $pwd = \lib\Hash::encode($pwd);
            $dba = $this->dba;
            $sql = "update SysUser
                    set UserPassword = ?
                      where UserID=?";
            $result = $dba->exec($sql, [$pwd, $id]);
            $this->result = $result;
        } else {
            $this->result = false;
        }
    }

    public function test()
    {
        echo "<pre>";
        $dba = $this->dba;
        $times = 300;
        while ($times-- > 0) {
            $sql = "select * from SysUser;select * from SysUser;";
            $params = [$this->session["choice"]];
            print_r($dba->getAll($sql));
            $sql = "select MaxRoutingCalls, MaxCalls, CallWaitingTime, Suspend from SysUser with (nolock) where UserID=?;

                select count(1) as totalCalls
                from CustomerLists as a WITH (NOLOCK)
                left join RegisteredLogs as c WITH (NOLOCK) on a.ExtensionNo=c.CustomerNO
                where a.UserID='{$this->session['choice']}' AND
                c.ETime > GETDATE() and
                c.ETime is not null;

                select CalledId, CallDuration, Seat
                   from CallState with (nolock)
                where CallDuration='0' and (ExtensionNo='' or ExtensionNo is null);";
            print_r($dba->getAll($sql, $params));
            $sql = "update SysUser set UserID='lu7766' where UserID='lu7766';";
            print_r($dba->exec($sql));
            ob_flush();
            flush();
        }
    }
}

?>
