<?php

class SysSweep_Model extends JModel
{
    public function sweepStatus()
    {
        $dba = $this->dba;
        $params = [$this->session["choice"]];
        $sql = "select
                  MaxRoutingCalls, MaxSearchCalls, SearchSuspend, SearchAutoStartTime, SearchAutoStopTime,
                  MaxRegularCalls, MaxCalls
                from SysUser with (nolock)
                where UserID=?;
                ";
        $this->data1 = $dba->getAll($sql, $params);
        $this->is_suspend = $this->data1[0]["SearchSuspend"] != '1';
        $this->maxRoutingCalls = $this->data1[0]["MaxRoutingCalls"];
        $this->maxSearchCalls = $this->data1[0]["MaxSearchCalls"];
        $this->searchAutoStartTime = $this->data1[0]["SearchAutoStartTime"];
        $this->searchAutoStopTime = $this->data1[0]["SearchAutoStopTime"];
        $this->maxRegularCalls = $this->data1[0]["MaxRegularCalls"];
        $this->maxCalls = $this->data1[0]["MaxCalls"];
        return $this;
    }

    public function getAjaxSweepStatusContent()
    {
        $dba = $this->dba;
        $params = [$this->session["choice"]];
        $sql = "select
                  UserID, CallOutID, StartDateTime, StartCalledNumber,
                  StartDateTime,CalledCount, RunTimeCount,
                  CallUnavailable, CallAvailable, CallConCount,
                  TotalFee, UseState, PlanName, NumberMode
                from SearchPlan with (nolock)
                where UserID=?
                order by CallOutID desc";
        $this->data3 = $dba->getAll($sql, $params);
        $this->waitExtensionNoCount = $dba->getAll("
            SELECT count(*) as count from CallState WHERE CallDuration>0 AND (ExtensionNo is NULL OR ExtensionNo='' ) AND UserID='{$this->session["choice"]}';
        ")[0]["count"];
        $this->extensionNoCount = $dba->getAll("
            SELECT count(*) as count from CallState WHERE CallDuration>0 AND (ExtensionNo is not NULL OR ExtensionNo<>'' )  AND UserID='{$this->session["choice"]}';
        ")[0]["count"];
        $sql = "select Balance
                   from SysUser with (nolock)
                where UserID=?";
        $this->balance = number_format($dba->getAll($sql, $params)[0]["Balance"], 2, ".", "");
        return $this;
    }

    public function chgConcurrentCalls()
    {
        $dba = $this->dba;
        $sql = "update SearchPlan set ConcurrentCalls=? where UserID=? and CallOutID=?;";
        $params = [$this->concurrentCalls, $this->userId, $this->calloutId];
        return $dba->exec($sql, $params);
    }

    public function chgCalloutGroupID()
    {
        $dba = $this->dba;
        $sql = "update SearchPlan set CalloutGroupID=? where UserID=? and CallOutID=?;";
        $params = [$this->calloutGroupID, $this->userId, $this->calloutId];
        return $dba->exec($sql, $params);
    }

    public function deleteSearchPlan()
    {
        $dba = $this->dba;
        $sql = "delete from SearchPlan where UserID=? and CallOutID=?;
                delete from SearchNumberList where CallOutID=?;";
        $params = [$this->userId, $this->callOutId, $this->callOutId];
        return $dba->exec($sql, $params);
    }

    public function updateRecall()
    {
        $dba = $this->dba;
        $sql = "update SearchPlan set RunTimeCount=? where UserID=? and CallOutID=?;";
        $params = [0, $this->userId, $this->calloutId];
        return $dba->exec($sql, $params);
    }

    public function updateSearchSuspendSwitch()
    {
        $dba = $this->dba;
        $sql = "update SysUser set SearchSuspend=abs(SearchSuspend-1) where UserID=?";
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

    public function updateMaxSearchCalls()
    {
        $dba = $this->dba;
        $sql = "update SysUser set MaxSearchCalls=? where UserID=?";
        $params = [$this->maxSearchCalls, $this->choice];
        return $dba->exec($sql, $params);
    }

    public function updateSearchAutoStartTime()
    {
        $dba = $this->dba;
        $sql = "update SysUser set SearchAutoStartTime=? where UserID=?";
        $params = [$this->searchAutoStartTime, $this->choice];
        return $dba->exec($sql, $params);
    }

    public function updateSearchAutoStopTime()
    {
        $dba = $this->dba;
        $sql = "update SysUser set SearchAutoStopTime=? where UserID=?";
        $params = [$this->searchAutoStopTime, $this->choice];
        return $dba->exec($sql, $params);
    }

    public function updateUseState()
    {
        $dba = $this->dba;
        $sql = "update SearchPlan set UseState=? where UserID=? and CallOutID=?";
        $params = [$this->useState, $this->userId, $this->callOutId];
        return $dba->exec($sql, $params);
    }

    public function getDownloadCallUnavailable()//無效
    {
        $sql = "select CalledNumber from SearchNumberList where CallResult='1' and CallOutID=?";
        $this->createFile($sql, [$this->callOutId]);
    }

    private function createFile($sql, $params = [], $suffix = "")
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
        @mkdir($this->base["sweep"]);
        file_put_contents($this->fileName, $txt);
        rename($this->fileName, $this->filePath);
        $pastMonth = date("Y-m", strtotime('-2 month'));
        $this->delTreePrefix($this->base["sweep"], $pastMonth);
    }

    public function getDownloadCallAvailable()//有效
    {
        $sql = "select CalledNumber from SearchNumberList where CallResult='2' and CallOutID=?";
        $this->createFile($sql, [$this->callOutId]);
    }

    public function getDownloadCallConCount()//接通
    {
        $sql = "select CalledNumber from SearchNumberList where CallResult='3' and CallOutID=?";
        $this->createFile($sql, [$this->callOutId]);
    }
}

?>
