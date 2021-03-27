<?php

use comm\DB;
use comm\SQL;
use comm\Rules;

class AdCallSetting_Model extends JModel
{
    /**
     * 上傳廣告群乎音源檔
     * @return $this|void
     */
    public function uploadFileAndConvert($fieldName = "voiceFile")
    {
        return lib\VoiceRecord::uploadFile($this->session['choice'], $fieldName);
    }

    /**
     * 廣告群乎音源檔全部刪除
     * @return $this|void
     */
    public function deleteAllVoiceFile()
    {
        foreach ($this->fileNames as $fileName) {
            $this->deleteVoiceFile($fileName);
        }
    }

    /**
     * 廣告群乎音源檔刪除
     * @return $this|void
     */
    public function deleteVoiceFile($fileName)
    {
        lib\VoiceRecord::delFile($this->session['choice'], $fileName);
    }

    /**
     * 新增廣告群乎
     * @return $this|void
     */
    public function postAdCallSchedule()
    {
        $max_limit = 500000;
        $result = [];
        $len = 0;
        if ($this->numberMode == "1") {
            $result = $this->readUploadList();
            if ($result == -1 || !$result) {
                $this->warning = "上傳錯誤，請更換檔案或聯繫系統管理員";
                return;
            }
            $result = \comm\Rules::filter(array_unique($result), [
                'phone_rule' => ''
            ])['result'];
            $len = count($result);
            if (!$len) {
                $this->warning = "筆數異常，請重新檢查檔案";
                return;
            }
            if (!strlen($result[0])) {
                $this->warning = $result[0] . "起始電話異常，請重新檢查檔案";
                return;
            }
            $this->startCalledNumber = $result[0];
            $this->calledCount = $len;
            if ($len > $max_limit) {
                $this->warning = "筆數不得超過50萬筆";
                return;
            }
        }
        if ($this->calledCount > $max_limit) {
            $this->warning = "筆數不得超過20萬筆";
            return;
        }
        $callOutID = DB::table('AdPlan')->select('max(CallOutID)+1 as count')->get()[0]["count"];
        $callOutID = empty($callOutID) ? "1" : $callOutID;
        DB::table('AdPlan')->insert([
            'UserID'            => $this->session["choice"],
            'PlanName'          => $this->planName,
            'StartCalledNumber' => $this->startCalledNumber,
            'CalledCount'       => $this->calledCount,
            'CallProgressTime'  => $this->callProgressTime,
            'UseState'          => 0,
            'ConcurrentCalls'   => $this->concurrentCalls,
            'NumberMode'        => $this->numberMode,
            'CallOutID'         => $callOutID,
            'StartDate'         => $this->startDate,
            'StartTime1'        => $this->startTime1,
            'StartTime2'        => $this->startTime2,
            'StartTime3'        => $this->startTime3,
            'StopDate'          => $this->stopDate,
            'StopTime1'         => $this->stopTime1,
            'StopTime2'         => $this->stopTime2,
            'StopTime3'         => $this->stopTime3,
            'FileName1'         => $this->fileName1,
            'FileName2'         => $this->fileName2,
            'CallRetry'         => $this->callRetry,
            'RetryTime'         => $this->retryTime,
            'StopOnConCount'    => $this->stopOnConCount,
            'WaitDTMF'          => $this->waitDTMF ?? 0
        ])->exec();
        $phone_len = strlen($this->startCalledNumber);
        $body = [];
        switch ($this->numberMode) {
            case "0"://range
                while ($this->calledCount-- > 0) {
                    $body[] = [
                        'CallOutID'    => $callOutID,
                        'CalledNumber' => str_pad($this->startCalledNumber++, $phone_len, '0', STR_PAD_LEFT)
                    ];
                }
                break;
            case "1"://list
                while ($len-- > 0) {
                    $body[] = [
                        'CallOutID'    => $callOutID,
                        'CalledNumber' => array_shift($result)
                    ];
                }
                break;
        }
//        \lib\QueueSQL::write($sql);
        $this->chunkInsertDB2('AdNumberList', $body);
        return $this;
    }

    /**
     * 取得廣告群乎
     * @return $this|void
     */
    public function getAdCallSchedule()
    {
        $this->data = DB::table('AdPlan')
            ->select('UserID', 'CallOutID', 'PlanName', 'StartCalledNumber', 'CalledCount', 'UseState')
            ->where('UserID', $this->session["choice"])
            ->orderBy('CallOutID', 'desc')
            ->get();
    }

    /**
     * 刪除廣告群乎
     * @return $this|void
     */
    public function deleteAdCallSchedule()
    {
        if (!is_array($this->delete)) {
            return;
        }
        $db = DB::table('AdPlan')->delete();
        foreach ($this->delete as $delete) {
            list($userId, $callOutId) = explode(",", $delete);
            $where = [
                ['UserID', $userId],
                ['CallOutID', $callOutId]
            ];
            !$db->hasWhere() ? $db->where($where) : $db->orWhere($where);
            DB::table('AdNumberList')->delete()->where('CallOutID', $callOutId)->exec();
        }
        $db->exec();
    }

    /**
     * 取得廣告群乎單筆資料
     * @return $this|void
     */
    public function getAdCallScheduleDetail()
    {
        $this->data = DB::table('AdPlan as a')->select([
            'b.UserName',
            'b.Balance',
            'a.CallProgressTime',
            'a.UseState',
            'a.PlanName',
            'a.StartCalledNumber',
            'a.CalledCount',
            'a.ConcurrentCalls',
            'a.StartDate',
            'a.StartTime1',
            'a.StartTime2',
            'a.StartTime3',
            'a.StopDate',
            'a.StopTime1',
            'a.StopTime2',
            'a.StopTime3',
            'a.FileName1',
            'a.FileName2',
            'a.CallRetry',
            'a.RetryTime',
            'a.StopOnConCount',
            'a.WaitDTMF',
            'a.TotalFee'
        ])->leftJoin('SysUser as b', 'a.UserID', '=', 'b.UserID')->where([
            ['a.UserID', $this->userId],
            ['a.CallOutID', $this->callOutId]
        ])->get()[0];
    }

    /**
     * 更新廣告群呼
     * @return $this|void
     */
    public function updateAdCallScheduleDetail()
    {
        DB::table('AdPlan')->update([
            'PlanName'          => $this->planName,
            'StartCalledNumber' => $this->startCalledNumber,
            'CalledCount'       => $this->calledCount,
            'CallProgressTime'  => $this->callProgressTime,
            'ConcurrentCalls'   => $this->concurrentCalls,
            'StartDate'         => $this->startDate,
            'StartTime1'        => $this->startTime1,
            'StartTime2'        => $this->startTime2,
            'StartTime3'        => $this->startTime3,
            'StopDate'          => $this->stopDate,
            'StopTime1'         => $this->stopTime1,
            'StopTime2'         => $this->stopTime2,
            'StopTime3'         => $this->stopTime3,
            'FileName1'         => $this->fileName1,
            'FileName2'         => $this->fileName2,
            'CallRetry'         => $this->callRetry,
            'RetryTime'         => $this->retryTime,
            'StopOnConCount'    => $this->stopOnConCount,
            'UseState'          => $this->useState ?? 0,
            'WaitDTMF'          => $this->waitDTMF ?? 0,
        ])->where([
            ['UserID', $this->userId],
            ['CallOutID', $this->callOutId]
        ])->exec();
        if ($this->calledCount != $this->calledCount_source) {
            DB::table('AdNumberList')->delete()->where('CallOutID', $this->callOutId)->exec();
            $phone_len = strlen($this->startCalledNumber);
            switch ($this->numberMode) {
                case "0"://range
                    while ($this->calledCount-- > 0) {
                        DB::table('AdNumberList')->insert([
                            'CallOutID'    => $this->callOutId,
                            'CalledNumber' => str_pad($this->startCalledNumber++, $phone_len, '0', STR_PAD_LEFT)
                        ])->exec();
                    }
                    break;
            }
        }
    }

    ////////////////////////////////////////////////////////////////////

    /**
     * 廣告群乎狀態
     */
    public function getAdCallStatus()
    {
        $user = DB::table('SysUser')
            ->select('MaxRoutingCalls',
                'MaxCalls',
                'PlanDistribution',
                'AdSuspend',
                'MaxRegularCalls',
                'MaxSearchCalls',
                'AdNote')
            ->where('UserID', $this->session["choice"])->get()[0];
        $this->is_suspend = $user["AdSuspend"] != '1';
        $this->maxRoutingCalls = $user["MaxRoutingCalls"];
        $this->maxCalls = $user["MaxCalls"];
        $this->planDistribution = $user["PlanDistribution"];
        $this->adSuspend = $user["AdSuspend"];
        $this->maxRegularCalls = $user["MaxRegularCalls"];
        $this->maxSearchCalls = $user["MaxSearchCalls"];
        $this->adNote = $user["AdNote"];
    }

    public function getAjaxAdCallStatusContent()
    {
        $sql = "select
                  UserID, CallOutID, StartCalledNumber,
                  CalledCount, RunTimeCount, CallConCount,
                  CallUnavailable, ConcurrentCalls, CalloutCount,
                  TotalFee, UseState, PlanName, CallRetry,
                  (SELECT COUNT(*) FROM AdNumberList WHERE CallOutID=AdPlan.CallOutID AND CallResult=1 AND CallCount<AdPlan.CallRetry+1) as UnRecieveWaitCall
                from AdPlan with (nolock)
                where UserID=?
                order by CallOutID desc";
        $params = [$this->session["choice"]];
        $this->data3 = $this->dba->getAll($sql, $params);
        $db = DB::table('CallState')
            ->select('count(*) as count')
            ->where('CallDuration', '>', 0)
            ->andWhere('UserID', $this->session['choice']);
        $db2 = clone $db;
        $this->waitExtensionNoCount = $db->andWhere([
            ['ExtensionNo', SQL::IS, null],
            ['ExtensionNo', '']
        ], SQL:: OR)
            ->get()['count'];
        $this->extensionNoCount = $db2->andWhere([
            ['ExtensionNo', SQL::IS, null],
            ['ExtensionNo', '<>', '']
        ], SQL:: OR)
            ->get()['count'];
        $this->balance = number_format(
            DB::table('SysUser')->select('Balance')->where('UserID', $this->session["choice"])->get()[0]["Balance"]
            , 2, ".", "");
    }

    public function updateAdSuspend()
    {
        $dba = $this->dba;
        $sql = "update SysUser set AdSuspend=abs(AdSuspend-1) where UserID=?";
        $params = [$this->userId];
        return $dba->exec($sql, $params);
    }

    public function updateUserAdNote()
    {
        $dba = $this->dba;
        $sql = "update SysUser set AdNote=? where UserID=?";
        $params = [nl2br2($this->adNote), $this->userId];
        return $dba->exec($sql, $params);
    }

    public function updateConcurrentCalls()
    {
        $dba = $this->dba;
        $sql = "update AdPlan set ConcurrentCalls=? where UserID=? and CallOutID=?;";
        $params = [$this->concurrentCalls, $this->userId, $this->calloutId];
        return $dba->exec($sql, $params);
    }

    public function updateUseState()
    {
        $dba = $this->dba;
        $sql = "update AdPlan set UseState=? where UserID=? and CallOutID=?";
        $params = [$this->useState, $this->userId, $this->callOutId];
        return $dba->exec($sql, $params);
    }

    public function deleteAdPlan()
    {
        $dba = $this->dba;
        $sql = "delete from AdPlan where UserID=? and CallOutID=?;
                delete from AdNumberList where CallOutID=?;";
        $params = [$this->userId, $this->callOutId, $this->callOutId];
        return $dba->exec($sql, $params);
    }

    /**
     * 廣告呼叫狀態下載
     * 筆數
     */
    public function getDownloadCalledCount()
    {
        $sql = "select CalledNumber from AdNumberList where CallOutID=? order by OrderKey";
        $params = [$this->callOutID];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 待發
     */
    public function getDownloadWaitCall()
    {
        $sql = "select CalledNumber from AdNumberList where CallOutID=? and CallResult=? order by OrderKey";
        $params = [$this->callOutID, 0];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 執行
     */
    public function getDownloadCalloutCount()
    {
        $sql = "select CalledNumber from AdNumberList where CallOutID=? and CallResult<>? order by OrderKey";
        $params = [$this->callOutID, 0];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 接聽數
     */
    public function getDownloadCallConCount()
    {
        $sql = "select CalledNumber from AdNumberList where CallOutID=? and CallResult=? order by OrderKey";
        $params = [$this->callOutID, 3];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 未接通待發
     */
    public function getDownloadUnRecieveWaitCall()
    {
        $sql = "select CalledNumber FROM AdNumberList WHERE CallOutID=? AND CallResult=1 AND CallCount<?+1 order by OrderKey";
        $params = [$this->callOutID, $this->callRetry];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 未接通
     */
    public function getDownloadCallUnavailable()
    {
        $sql = "select CalledNumber FROM AdNumberList WHERE CallOutID=? AND CallResult=1 order by OrderKey";
        $params = [$this->callOutID];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * 廣告通聯查詢
     */
    public function getAdCommunicationSearch()
    {
        $query = DB::table('AdCDR')->select([
            'OrgCalledId',
            'CalledCalloutDate',
            "CallStartBillingDate + ' ' + CallStartBillingTime as CallStartBillingDateTime",
            'CallLeaveDateTime',
            'CallDuration',
            'RecvDTMF',
            'FaxCall'
        ])->where('UserID', $this->session['choice']);
        if ($this->CalledCalloutDateStart)// getdate()
        {
            $query->andWhere('cast(CalledCalloutDate as datetime)', '>=', $this->CalledCalloutDateStart);
        }
        if ($this->CalledCalloutDateStop) {
            $query->andWhere('cast(CalledCalloutDate as datetime)', '<=', $this->CalledCalloutDateStop);
        }
        if ($this->PlanName) {
            $query->andWhere('PlanName', $this->PlanName);
        }
        if ($this->CallBill > -1) {
            $query->andWhere('CallBill', $this->CallBill);
        }
        $query->orderBy(function ($db) {
            return $db->cast('CalledCalloutDate', 'datetime');
        }, 'desc');
        $this->data = $query->get();
    }

    /**
     * 廣告通聯查詢
     */
    public function getAdCommunicationSearch2()
    {
        $dba = $this->dba;
        $sql = "select ROW_NUMBER() over (order by CalledCalloutDate desc,LogID) rownum,
                  OrgCalledId, CalledCalloutDate,
                  CallStartBillingDate+' '+CallStartBillingTime as CallStartBillingDateTime,
                  CallLeaveDateTime, CallDuration
                from AdCDR with (nolock) where UserID='{$this->session['choice']}' and ";
        $sql2 = "select count(1) as count ,
                  sum(CallDuration) as TotalTime,
                  sum(cast(BillValue as float)) as TotalMoney
                  from AdCDR with (nolock)
                  where UserID='{$this->session['choice']}' and ";
        $sql3 = "select count(1) as TotalConnected
                  from AdCDR with (nolock)
                  where UserID='{$this->session['choice']}' and CallDuration is not null and CallDuration > 0 and ";
        $condition = [];
        $params = [];
        if ($this->CalledCalloutDateStart)// getdate()
        {
            $condition[] = " cast(CalledCalloutDate as datetime) >= ? ";
            $params[] = $this->CalledCalloutDateStart;
        }
        if ($this->CalledCalloutDateStop) {
            $condition[] = " cast(CalledCalloutDate as datetime) <= ? ";
            $params[] = $this->CalledCalloutDateStop;
        }
        if ($this->PlanName) {
            $condition[] = " PlanName = ? ";
            $params[] = $this->PlanName;
        }
        if ($this->CallBill > -1) {
            $condition[] = " CallBill = ? ";
            $params[] = $this->CallBill;
        }
        if ($this->SearchSec > 0) {
            $condition[] = " CallDuration >= ? ";
            $params[] = $this->SearchSec;
        }
        if ($this->RecvDTMF) {
            $condition[] = " RecvDTMF = ? ";
            $params[] = $this->RecvDTMF;
        }
        if (count($condition)) {
            $sql .= join(" and ", $condition);
            $sql2 .= join(" and ", $condition);
            $sql3 .= join(" and ", $condition);
        } else {
            $sql = substr($sql, 0, -5);
            $sql2 = substr($sql, 0, -5);
            $sql3 = substr($sql, 0, -5);
        }
        $result = $dba->getAll($sql2, $params)[0];
        $this->count = $result['count'];
        $this->totalTime = $result['TotalTime'];
        $this->totalMoney = $result['TotalMoney'];
        $this->totalConnected = $dba->getAll($sql3, $params)[0]['TotalConnected'];
        //die(json_encode($this->count));
        $this->data = $dba->getAllLimit($sql, $params, $this->offset, $this->limit);
    }
}

?>
