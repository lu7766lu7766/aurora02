<?php

class SysLookout_Model extends JModel
{
    public function callStatus()
    {


        $dba = $this->dba;
        $sql = "select MaxRoutingCalls, MaxCalls, CallWaitingTime, Suspend, PlanDistribution,
                MaxRegularCalls, MaxSearchCalls
                from SysUser with (nolock)
                where UserID=?;";
        $params = [$this->session["choice"]];
        $this->data1 = $dba->getAll($sql, $params);
        $this->is_suspend = $this->data1[0]["Suspend"] != '1';
        $this->maxRoutingCalls = $this->data1[0]["MaxRoutingCalls"];
        $this->maxCalls = $this->data1[0]["MaxCalls"];
        $this->callWaitingTime = $this->data1[0]["CallWaitingTime"];
        $this->planDistribution = $this->data1[0]["PlanDistribution"];
        $this->maxRoutingCalls = $this->data1[0]["MaxRoutingCalls"];
        $this->maxSearchCalls = $this->data1[0]["MaxSearchCalls"];
        $this->maxRegularCalls = $this->data1[0]["MaxRegularCalls"];
        return $this;
    }

    public function getPing()
    {
        $pingresult = exec("ping $this->ip", $outcome, $status);
//    if (0 == $status) {
//        $status = "alive";
//    } else {
//        $status = "dead";
//    }
        //echo "The IP address, $ip, is  ".$status."^^<BR>";
        foreach ($outcome as $val) {
            echo iconv("BIG5", "UTF-8", $val) . "<br>";
        }
        return $status;
    }

    public function getAjaxCallStatusContent()
    {
        $dba = $this->dba;
        $sql = "select CalledId, CallDuration, Seat, NormalCall
                   from CallState with (nolock)
                where CallDuration='0' and (ExtensionNo='' or ExtensionNo is null) and UserID=?;";
//        $params = [$this->session["choice"]];
        $params = [$this->userId];
        $this->data1 = $dba->getAll($sql, $params);
        $sql = "select a.ExtensionNo, a.CalledId, a.CalloutGroupID, a.CallDuration, b.PingTime, a.Seat, a.NormalCall, a.OnMonitor
                   from CallState as a with (nolock)
                   left join RegisteredLogs as b with (nolock) on a.ExtensionNo = b.CustomerNO
                   where CallDuration > '0' and (ExtensionNo <> '' or ExtensionNo is not null) and UserID=?;";
        $this->data2 = $dba->getAll($sql, $params);
        $sql = "select UserID, CallOutID, PlanName,
                       StartCalledNumber, CalledCount, CalloutCount,
                       CallConCount, CallSwitchCount, UseState,
                       NumberMode, CalloutGroupID,ConcurrentCalls,
                       EndCalledNumber
                   from CallPlan with (nolock)
                where UserID=?";
        $this->data3 = $dba->getAll($sql, $params);
        $this->waitExtensionNoCount = $dba->getAll("
            SELECT count(*) as count from CallState WHERE CallDuration>1 AND ExtensionNo='system' AND UserID=?;
        ", $params)[0]["count"];
        $this->extensionNoCount = $dba->getAll("
            SELECT count(*) as count from CallState WHERE CallDuration>0 AND (ExtensionNo is not NULL OR ExtensionNo<>'' )  AND UserID=?;
        ", $params)[0]["count"];
        $sql = "select Balance, Suspend
                   from SysUser with (nolock)
                where UserID=?";
        $res = $dba->getAll($sql, $params)[0];
        $this->balance = number_format($res["Balance"], 2, ".", "");
        $this->suspend = $res['Suspend'] != '1';
        return $this;
    }

    public function chgConcurrentCalls()
    {
        $dba = $this->dba;
        $sql = "update CallPlan set ConcurrentCalls=? where UserID=? and CallOutID=?;";
        $params = [$this->concurrentCalls, $this->userId, $this->calloutId];
        return $dba->exec($sql, $params);
    }

    public function chgCalloutGroupID()
    {
        $dba = $this->dba;
        $sql = "update CallPlan set CalloutGroupID=? where UserID=? and CallOutID=?;";
        $params = [$this->calloutGroupID, $this->userId, $this->calloutId];
        return $dba->exec($sql, $params);
    }

    public function deleteCallPlan()
    {
        $dba = $this->dba;
        $sql = "delete from CallPlan where UserID=? and CallOutID=?;
                delete from NumberList where CallOutID=?;";
        $params = [$this->userId, $this->callOutId, $this->callOutId];
        return $dba->exec($sql, $params);
    }

    public function updateRecall()
    {
        $dba = $this->dba;
        $sql = "update CallPlan set RunTimeCount=? where UserID=? and CallOutID=?;";
        $params = [0, $this->userId, $this->calloutId];
        return $dba->exec($sql, $params);
    }

    public function updateSuspendSwitch()
    {
        $dba = $this->dba;
        $sql = "update SysUser set Suspend=abs(Suspend-1) where UserID=?";
        $params = [$this->choice];
        return $dba->exec($sql, $params);
    }

    public function updateMaxRoutingCalls()
    {
        $dba = $this->dba;
        $sql = "update SysUser set MaxRoutingCalls=? where UserID=?";
        $params = [$this->maxRoutingCalls, $this->choice];
        return $dba->exec($sql, $params);
    }

    public function updateMaxCalls()
    {
        $dba = $this->dba;
        $sql = "update SysUser set MaxCalls=? where UserID=?";
        $params = [$this->maxCalls, $this->choice];
        return $dba->exec($sql, $params);
    }

    public function updateCallWaitingTime()
    {
        $dba = $this->dba;
        $sql = "update SysUser set CallWaitingTime=? where UserID=?";
        $params = [$this->callWaitingTime, $this->choice];
        return $dba->exec($sql, $params);
    }

    public function updatePlanDistribution()
    {
        $dba = $this->dba;
        $sql = "update SysUser set PlanDistribution=? where UserID=?";
        $params = [$this->planDistribution, $this->choice];
        return $dba->exec($sql, $params);
    }

    public function updateUseState()
    {
        $dba = $this->dba;
        $sql = "update CallPlan set UseState=? where UserID=? and CallOutID=?";
        $params = [$this->useState, $this->userId, $this->callOutId];
        return $dba->exec($sql, $params);
    }

    /**
     * 呼叫狀態下載
     * 筆數
     */
    public function getDownloadCalledCount()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=?";
        $params = [$this->callOutId];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    private function createFile($sql, $params, $suffix = "")
    {
        $dba = $this->dba;
        $result = $dba->getAll($sql, $params);
        $data = [];
        foreach ($result as $val) {
            $data[] = $val["CalledNumber"];
        }
        $txt = join("\r\n", $data);
        $this->fileName = date("Y-m-d", time()) . "_{$this->session['choice']}{$suffix}.txt";
        $this->filePath = "download/" . $this->fileName;
        @mkdir($this->base["download"]);
        @mkdir($this->base["callStatus"]);
        file_put_contents($this->fileName, $txt);
        rename($this->fileName, $this->filePath);
        $pastMonth = date("Y-m", strtotime('-2 month'));
        $this->delTreePrefix($this->base["callStatus"], $pastMonth);
    }

    /**
     * 待發
     */
    public function getDownloadWaitCall()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 0];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    /**
     * 執行
     */
    public function getDownloadCalloutCount()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult<>?";
        $params = [$this->callOutId, 0];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    public function getDownloadCallSwitchCount()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 3];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    /**
     * 接聽數
     */
    public function getDownloadCallConCount()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 3];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    /**
     * 失敗下載
     */
    public function getDownloadFaild()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 1];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}_fail");
    }

    /**
     * 未接
     */
    public function getDownloadMissed()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 2];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}_missed");
    }
}

?>
