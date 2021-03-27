<?php

use comm\DB;
use comm\DBA;

class GroupCallSetting_Model extends JModel
{
    public function __construct($base)
    {
        parent::__construct($base);
        // 名單數限制
        $this->phone_limit = getenv2("GROUP_CALL_PHONE_LIMIT", 200000);
        // 排程數限制
        $this->list_limit = getenv2("GROUP_CALL_LIST_LIMIT", 99999);
    }

    public function postGroupCallSchedule()
    {
        // 有效號新增
        if ($this->numberMode == "3") {
            $result = array_map(function($x) {
                return $x["number"];
                }, $this->getEffectiveNumberByGroupCall($this->startCalledNumber, $this->calledCount));
            return $this->postEffectiveGroupCall($result);
        }
        // 排成限制
        if (!$this->checkCallPlanCount()) return ;
        $result = [];
        $len = 0;
        // 新增類型
        if ($this->numberMode == "1") {
            $result = $this->readUploadList();
            $len = count($result);
            if (!$len) {
                $this->warning = "筆數異常，請重新檢查檔案";
                return;
            }
            if (!strlen($result[0])) {
                $this->warning = "起始電話異常，請重新檢查檔案";
                return;
            }
            $this->startCalledNumber = $result[0];
            $this->calledCount = count($result);
            if ($len > $this->phone_limit) {
                $this->warning = "筆數不得超過{$this->phone_limit}筆";
                return;
            }
        }
        // 號碼名單數限制
        if ($this->calledCount > $this->phone_limit) {
            $this->warning = "筆數不得超過{$this->phone_limit}筆";
            return;
        }
        $callOutID = DB::table('CallPlan')->select('max(CallOutID)+1 as count')->get()[0]["count"];//確保寫入numberList的calloutid是唯一值
        $callOutID = empty($callOutID) ? "1" : $callOutID;
        DB::table('CallPlan')->insert([
            'UserID'            => $this->session["choice"],
            'PlanName'          => $this->planName,
            'StartCalledNumber' => $this->startCalledNumber,
            'CalledCount'       => $this->calledCount,
            'CallerPresent'     => 1,
            'CallerID'          => '',
            'CalloutGroupID'    => $this->calloutGroupID,
            'Calldistribution'  => $this->calldistribution,
            'CallProgressTime'  => $this->callProgressTime,
            'ExtProgressTime'   => $this->extProgressTime,
            'UseState'          => 0,
            'ConcurrentCalls'   => $this->concurrentCalls,
            'NumberMode'        => $this->numberMode,
            'CallOutID'         => $callOutID
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
            case "2"://same
                while ($this->calledCount-- > 0) {
                    $body[] = [
                        'CallOutID'    => $callOutID,
                        'CalledNumber' => $this->startCalledNumber
                    ];
                }
                break;
        }
        $this->chunkInsertDB2('NumberList', $body);
        return $this;
    }

    private function getCallPlanCount() {
        return DB::table('CallPlan')
            ->select('count(1) as count')
            ->where('UserID', $this->session["choice"])
            ->get()[0]["count"];
    }

    public function getGroupCallSchedule()
    {
        $this->data = DB::table('CallPlan')
            ->select('UserID', 'CallOutID', 'PlanName', 'StartCalledNumber', 'CalledCount', 'Calldistribution',
                'UseState')
            ->where('UserID', $this->session["choice"])
            ->orderBy('CallOutID', 'desc')
            ->get();
    }

    public function deleteGroupCallSchedule()
    {
        if (!is_array($this->delete)) {
            return;
        }
        $db = DB::table('CallPlan')->delete();
        foreach ($this->delete as $delete) {
            list($userId, $callOutId) = explode(",", $delete);
            $where = [
                ['UserID', $userId],
                ['CallOutID', $callOutId]
            ];
            !$db->hasWhere() ? $db->where($where) : $db->orWhere($where);
            DB::table('NumberList')->delete()->where('CallOutID', $callOutId)->exec();
        }
        $db->exec();
    }

    public function getGroupCallScheduleDetail()
    {
        $dba = $this->dba;
        $sql = "select b.UserName,b.Balance,b.CallWaitingTime,
                       a.CallProgressTime,a.CallerPresent,a.CallerID,
                       a.CalloutGroupID,a.Calldistribution,a.CallProgressTime,
                       a.ExtProgressTime,a.UseState,a.PlanName,
                       a.StartCalledNumber,a.CalledCount,a.ConcurrentCalls,
                       a.NumberMode
                from CallPlan as a WITH (NOLOCK)
                left join SysUser as b WITH (NOLOCK) on a.UserID=b.UserID
                where a.UserID=? and a.CallOutID=?";
        $params = [
            $this->userId,
            $this->callOutId
        ];
        $this->data = $dba->getAll($sql, $params)[0];
    }

    public function updateGroupCallScheduleDetail()
    {
        DB::table('SysUser')->update([
            'CallWaitingTime' => $this->callWaitingTime
        ])->where('UserID', $this->userId)->exec();
        DB::table('CallPlan')->update([
            'PlanName'          => $this->planName,
            'StartCalledNumber' => $this->startCalledNumber,
            'CalledCount'       => $this->calledCount,
            'CallerPresent'     => $this->callerPresent,
            'CallerID'          => $this->callerID,
            'CalloutGroupID'    => $this->calloutGroupID,
            'Calldistribution'  => $this->calldistribution,
            'CallProgressTime'  => $this->callProgressTime,
            'ExtProgressTime'   => $this->extProgressTime,
            'ConcurrentCalls'   => $this->concurrentCalls,
            'UseState'          => $this->useState ?? 0
        ])->where([
            ['UserID', $this->userId],
            ['CallOutID', $this->callOutId]
        ])->exec();
        if ($this->calledCount != $this->calledCount_source) {
            $phone_len = strlen($this->startCalledNumber);
            switch ($this->numberMode) {
                case "0"://range
                    DB::table('NumberList')->delete()->where('CallOutID', $this->callOutId)->exec();
                    while ($this->calledCount-- > 0) {
                        DB::table('NumberList')->insert([
                            'CallOutID'    => $this->callOutId,
                            'CalledNumber' => str_pad($this->startCalledNumber++, $phone_len, '0', STR_PAD_LEFT)
                        ])->exec();
                    }
                    break;
                case "2"://same
                    DB::table('NumberList')->delete()->where('CallOutID', $this->callOutId)->exec();
                    while ($this->calledCount-- > 0) {
                        DB::table('NumberList')->insert([
                            'CallOutID'    => $this->callOutId,
                            'CalledNumber' => $this->startCalledNumber
                        ])->exec();
                    }
                    break;
            }
        }
    }

    public function getEffectiveNumber()
    {
        $this->searchNumber = preg_replace("/\?/", "_", $this->searchNumber);
//        if($this->countryCode==0) // taiwan
//        {
//            $result = $this->dba->getAll("select DISTINCT OrgCalledId as number from CalloutCDR with (nolock) where OrgCalledId like ? and CallBill='1';",
//                [$this->searchNumber]);
//        }
//        else if($this->countryCode==1)//china
//        {
//        }
        $dba2 = new DBA();
        $dba2->dbHost = "125.227.84.247";
        $dba2->dbName = "NumberCollector";
        $dba2->connect();
        $result = $dba2->getAll("select DISTINCT CalledNumber as number from AllNumberList with (nolock) where CalledNumber like ? and CallResult in ('2','3');",
            [$this->searchNumber]);
        if (count($result) > $this->phone_limit) {
            echo json_encode(["len" => -1, "status" => -1,]);
            return;
        }
        $limit = 5000;
        $list = [];
        foreach ($result as $item) {
            $list[] = $item["number"];
        }
        $len = ceil(count($list) / $limit);
        $i = 0;
        while ($i < $len) {
            $this->postDefaultSchedule(array_slice($list, $i * $limit, $limit));
            $i++;
        }
        echo json_encode(["len" => count($result), "status" => 0]);
    }

    public function postDefaultSchedule($list)
    {
        $list = is_string($list) ? explode(",", $list) : $list;
        $len = count($list);
        $callOutID = DB::table('CallPlan')->select('max(CallOutID)+1 as count')->get()[0]["count"];//確保寫入numberList的calloutid是唯一值
        $callOutID = empty($callOutID) ? "1" : $callOutID;
        DB::table('CallPlan')->insert([
            'UserID'            => $this->session["choice"],
            'PlanName'          => $list[0],
            'StartCalledNumber' => $list[0],
            'CalledCount'       => $len,
            'CallerPresent'     => 1,
            'CallerID'          => '',
            'CalloutGroupID'    => 1, //座群
            'Calldistribution'  => 1,//自動均配
            'CallProgressTime'  => 20,//撥出電話等待秒數
            'ExtProgressTime'   => 15,//轉分機等待秒數
            'UseState'          => 0,//$this->useState??
            'ConcurrentCalls'   => 10,//自動撥號速度
            'NumberMode'        => 1,//名單上傳
            'CallOutID'         => $callOutID
        ])->exec();
        while ($len-- > 0) {
            DB::table('NumberList')->insert([
                'CallOutID'    => $callOutID,
                'CalledNumber' => array_shift($list)
            ])->exec();
        }
    }

    public function getEffectiveNumber2()
    {
        $this->searchNumber = preg_replace("/\?/", "_", $this->searchNumber);
        if ($this->countryCode == "0") // taiwan
        {
            $result = $this->dba->getAll("select DISTINCT OrgCalledId as number from CalloutCDR with (nolock) where OrgCalledId like ? and CallBill='1';",
                [$this->searchNumber]);
        } else {
            if ($this->countryCode == "1")//china
            {
                $dba2 = new DBA();
                $dba2->dbHost = "125.227.84.247";
                $dba2->dbName = "NumberCollector";
                $dba2->connect();
                $result = $dba2->getAll("select DISTINCT CalledNumber as number from AllNumberList with (nolock) where CalledNumber like ? and CallResult in ('2','3');",
                    [$this->searchNumber]);
            }
        }
        if (count($result) > $this->phone_limit) {
            echo json_encode(["data" => -1, "status" => -1,]);
            return;
        }
        echo json_encode(["data" => $result, "status" => 0, "country" => $this->countryCode]);
    }

    private function checkCallPlanCount() {
        if (($this->getCallPlanCount() + 1) > $this->list_limit) {
            $this->warning = "排程不得超過{$this->list_limit}筆";
            return false;
        }
        return true;
    }

    private function postEffectiveGroupCall($result) {
        if (!$this->checkCallPlanCount()) return ;
        $numbers = array_splice($result, 0, $this->phone_limit);
        $callOutID = DB::table('CallPlan')->select('max(CallOutID)+1 as count')->get()[0]["count"];//確保寫入numberList的calloutid是唯一值
        $callOutID = empty($callOutID) ? "1" : $callOutID;
        DB::table('CallPlan')->insert([
            'UserID'            => $this->session["choice"],
            'PlanName'          => $this->planName,
            'StartCalledNumber' => $numbers[0],
            'EndCalledNumber'   => end($numbers),
            'CalledCount'       => count($numbers),
            'CallerPresent'     => 1,
            'CallerID'          => '',
            'CalloutGroupID'    => $this->calloutGroupID,
            'Calldistribution'  => $this->calldistribution,
            'CallProgressTime'  => $this->callProgressTime,
            'ExtProgressTime'   => $this->extProgressTime,
            'UseState'          => 0,
            'ConcurrentCalls'   => $this->concurrentCalls,
            'NumberMode'        => $this->numberMode,
            'CallOutID'         => $callOutID
        ])->exec();
        $body = [];
        while ($number = array_shift($numbers)) {
            $body[] = [
                'CallOutID'    => $callOutID,
                'CalledNumber' => $number
            ];
        }
        $this->chunkInsertDB2('NumberList', $body);
        return count($result) > 0
            ? $this->postEffectiveGroupCall($result)
            : $this;
    }

    private function getEffectiveNumberByGroupCall($number, $count) {
        $dba2 = new DBA();
        $dba2->dbHost = "125.227.84.248";
        $dba2->dbName = "AcCdrSvr";
        $dba2->connect();
        $result = $dba2->getAll("select DISTINCT top $count OrgCalledId as number from AllCdrList with (nolock) where OrgCalledId >= ? order by OrgCalledId;",
            [$number]);
        return $result;
    }
}

?>
