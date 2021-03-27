<?php
//require_once "swop/library/dba.php";
//$dba=new dba();
use comm\DB;
use comm\SQL;

class CommunicationHistory_Model extends JModel
{
    public function __construct($base)
    {
        parent::__construct($base);
        // 單次上傳筆數限制
        $this->black_list_once_limit = getenv2("BLACK_LIST_ONCE_LIMIT", 10000);
    }

    public function getCommunicationSearch()
    {
        $dba = $this->dba;
        $page = $this->page = $this->page ?? 0;
        $per_page = $this->per_page = (!empty($this->per_page) ? $this->per_page : 100);
        $offset = $per_page * $page + 1;
        $limit = $per_page;
        $orderBy = " order by cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) desc";
        $sql1 = " select count(1) as count from CallOutCDR where";//,sum(cast(BillValue as int)) as totalMoney
        $sql2 = " select sum(CallDuration) as totalTime,sum(cast(BillValue as float)) as totalMoney from CallOutCDR where BillValue<>''  and";
        $sql = "select ROW_NUMBER() over ({$orderBy}) rownum,
                LogID,UserID, CallStartBillingDate, CallStartBillingTime, CallStopBillingDate, CallStopBillingTime,
                ExtensionNo, OrgCalledId, CallDuration, BillValue, RecordFile, CustomerLevel, CallType
                from CallOutCDR
                where";
        $params = [];
        if (!empty($this->userId)) {
            $sql1 .= " UserID = ?  and";
            $sql2 .= " UserID = ?  and";
            $sql .= " UserID = ?  and";
            $params[] = $this->userId;
        } else {
            $sql1 .= " UserID in (" . join(",", array_map(function ($v) {
                    return "'" . $v . "'";
                }, $this->session["current_sub_emp"])) . ")  and";
            $sql2 .= " UserID in (" . join(",", array_map(function ($v) {
                    return "'" . $v . "'";
                }, $this->session["current_sub_emp"])) . ")  and";
            $sql .= " UserID in (" . join(",", array_map(function ($v) {
                    return "'" . $v . "'";
                }, $this->session["current_sub_emp"])) . ")  and";
        }
        if (!empty($this->callStartBillingDate)) {
            $sql1 .= " cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) < ?  and";
            $sql2 .= " cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) < ?  and";
            $sql .= " cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) < ?  and";
            $params[] = $this->callStopBillingDate . " " . ($this->callStopBillingTime ?? "00:00:00");
        }
        if (!empty($this->callStopBillingDate)) {
//            $sql1 .= " cast((CallStopBillingDate+' '+CallStopBillingTime) as datetime) > ?  and";
//            $sql2 .= " cast((CallStopBillingDate+' '+CallStopBillingTime) as datetime) > ?  and";
//            $sql .= " cast((CallStopBillingDate+' '+CallStopBillingTime) as datetime) > ?  and";
            $sql1 .= " cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) > ?  and";
            $sql2 .= " cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) > ?  and";
            $sql .= " cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) > ?  and";
            $params[] = $this->callStartBillingDate . " " . ($this->callStartBillingTime ?? "00:00:00");
        }
        if (!empty($this->extensionNo)) {
            $sql1 .= " ExtensionNo = ?  and";
            $sql2 .= " ExtensionNo = ?  and";
            $sql .= " ExtensionNo = ?  and";
            $params[] = $this->extensionNo;
        }
        if (!empty($this->orgCalledId)) {
            $sql1 .= " OrgCalledId = ?  and";
            $sql2 .= " OrgCalledId = ?  and";
            $sql .= " OrgCalledId = ?  and";
            $params[] = $this->orgCalledId;
        }
        if (!empty($this->customerLevel)) {
            $sql1 .= " CustomerLevel = ?  and";
            $sql2 .= " CustomerLevel = ?  and";
            $sql .= " CustomerLevel = ?  and";
            $params[] = $this->customerLevel;
        }
        if (!empty($this->searchSec)) {
            $sql1 .= " CallDuration >= ?  and";
            $sql2 .= " CallDuration >= ?  and";
            $sql .= " CallDuration >= ?  and";
            $params[] = $this->searchSec;
        }
        if ($this->callType !== "") {
            $sql1 .= " CallType = ?  and";
            $sql2 .= " CallType = ?  and";
            $sql .= " CallType = ?  and";
            $params[] = $this->callType;
        }
        $sql1 = substr($sql1, 0, -5);
        $sql2 = substr($sql2, 0, -5);
        $sql = substr($sql, 0, -5);//and 前面兩個空白，防止沒有參數進來，清除where五個字
//$sql .= " order by cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) desc";
        $result = $dba->getAll($sql1, $params)[0];
        $this->rows = $result["count"];
//echo $this->rows."^^".$per_page;
        $this->last_page = ceil($this->rows / $per_page);
        $result = $dba->getAll($sql2, $params)[0];
        $this->totalTime = $result["totalTime"];
        $this->totalMoney = $result["totalMoney"];
//echo $this->totalTime."^^".$this->totalMoney;
//		Console::log($dba->mergeSQL($sql, $params));
//		Console::log($dba->mergeSQL($sql1, $params));
//		Console::log($dba->mergeSQL($sql2, $params));
// $tmp_data = $dba->getAll($sql2,$params);
// $this->totalTime = $tmp_data["totalTime"];
// $this->totalMoney = $tmp_data["totalMoney"];
//echo $offset."&^".$limit;
        $this->data = $dba->getAllLimit($sql, $params, $offset, $limit);
        $this->session["tmp_sql"] = $dba->mergeSQL($sql, $params);
        //echo $dba->mergeSQL($sql,$params)."^^";
    }

    public function getCommunicationSearchPageDatas()
    {
        $db = DB::table("CallOutCDR")
            ->select([
                "LogID",
                "UserID",
                "CallStartBillingDate",
                "CallStartBillingTime",
                "CallStopBillingDate",
                "CallStopBillingTime",
                "ExtensionNo",
                "OrgCalledId",
                "CallDuration",
                "BillValue",
                "RecordFile",
                "CustomerLevel",
                "CallType"
            ]);
        $db = $this->getCommunicationWhere($db);
        $page = $this->page = $this->page ? ($this->page - 1) : 0;
        $per_page = $this->per_page = (!empty($this->per_page) ? $this->per_page : 100);
        $offset = $per_page * $page + 1;
        $limit = $per_page;
        $db->orderBy("cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime)", "desc");
        $this->session["tmp_sql"] = $db->export();
//        print_r($this->session["tmp_sql"]);
        $this->data = $db->limit($offset, $limit)->get();
    }

    private function getCommunicationWhere($db)
    {
        if (!empty($this->userId)) {
            $db->where("UserID", $this->userId);
        } else {
            $db->whereIn("UserID", $this->session["current_sub_emp"]);
        }
        if (!empty($this->callStartBillingDate)) {
            $this->callStartBillingTime = $this->callStartBillingTime ?? '00:00:00';
            $db->andWhere("cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime)", ">",
                "{$this->callStartBillingDate} {$this->callStartBillingTime}");
        }
        if (!empty($this->callStopBillingDate)) {
            $this->callStopBillingTime = $this->callStopBillingTime ?? '00:00:00';
            $db->andWhere("cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime)", "<",
                "{$this->callStopBillingDate} {$this->callStopBillingTime}");
        }
        if (!empty($this->extensionNo)) {
            $db->andWhere("ExtensionNo", $this->extensionNo);
        }
        if (!empty($this->orgCalledId)) {
            $db->andWhere("OrgCalledId", $this->orgCalledId);
        }
        if (!empty($this->customerLevel)) {
            $db->andWhere("CustomerLevel", $this->customerLevel);
        }
        if ($this->searchSec !== "") {
            $db->andWhere("CallDuration", ">=", $this->searchSec);
        }
        if ($this->searchSec2 !== "") {
            $db->andWhere("CallDuration", "<=", $this->searchSec2);
        }
        if ($this->callType !== "") {
            $db->andWhere("CallType", $this->callType);
        }
        if (!$this->session["isRoot"]) {
            $db->andWhere(function ($db) {
                return $db->isNull("DeletedAt");
            });
        }
        return $db;
    }

    public function getCommunicationSearchCommonData()
    {
        $db = DB::table("CallOutCDR")
            ->select([
                "count(1) as count",
                "sum(CallDuration) as totalTime",
                "sum(cast(BillValue as float)) as totalMoney"
            ]);
        $db = $this->getCommunicationWhere($db);
        $result = $db->get()[0];
        $this->rows = $result["count"];
        $this->totalTime = $result["totalTime"];
        $this->totalMoney = $result["totalMoney"];
    }

    public function delCommunicationSearchCommonData()
    {
//        $db = DB::table("CallOutCDR")
//            ->update([
//                "DeletedAt" => date('Y-m-d H:i:s', time())
//            ])->whereIn('LogID', );
        if (!is_array($this->id) || !count($this->id)) {
            return false;
        }
        DB::table("CallOutCDR")
            ->update([
                "DeletedAt" => date('Y-m-d H:i:s', time())
            ])->whereIn('LogID', $this->id)->exec();
//        print_r($this->id);
//        echo DB::table("CallOutCDR")
//            ->update([
//                "DeletedAt" => date('Y-m-d H:i:s', time())
//            ])->whereIn('LogID', $this->id)->export();
        return true;
    }

    public function getCommunicationSearchDownload()
    {
        $this->writeCommunicationSearch($this->dba->getAll($this->session["tmp_sql"]));
    }

    private function writeCommunicationSearch($datas)
    {
        $content = "";
        foreach ($datas as $data) {
            $tmp_data = [
                $data["UserID"],
                $data["ExtensionNo"],
                $data["OrgCalledId"],
                $data["CallStartBillingDate"] . " " . $data["CallStartBillingTime"],
                $data["CallDuration"],
                $data["BillValue"],
                $data["CustomerLevel"]
            ];
            $content .= (join(",", $tmp_data) . "\r\n");
        }
        $this->fileName = date("Y-m-d",
                time()) . '-' . $this->session["choice"] . '.txt' . $this->session["login"]["UserID"];
//        echo $this->fileName . "^^";
        $fp = fopen($this->fileName, 'w');
        fwrite($fp, $content);
        fclose($fp);
        @mkdir($this->base["download"]);
        @mkdir($this->base["communicationSearch"]);
        if (file_exists($this->fileName)) {
            rename($this->fileName, $this->base["communicationSearch"] . $this->fileName);
        }
        $pastMonth = date("Y-m", strtotime('-2 month'));
        $this->delTreePrefix($this->base["communicationSearch"], $pastMonth);
    }

    public function getTaskRanking()
    {
        $dba = $this->dba;
        $this->display_mode = $this->display_mode ?? "0";
        $select_sql = $where_sql = $group_sql = "";
        switch ($this->display_mode) {
            case "0":
                $select_sql = "
select
a.UserID, a.ExtensionNo, b.UserName,
sum(cast(a.CallDuration as float)) as CallDuration,
COUNT(1) as Count,
sum(cast(BillValue as float)) as BillValue,
sum(cast(BillCost as float)) as BillCost
from CallOutCDR as a with (nolock)
left join SysUser as b  with (nolock) on a.UserID = b.UserID
where";
                $group_sql .= "group by a.UserID, a.ExtensionNo, b.UserName";
                break;
            case "1":
                $select_sql = "
select
a.UserID, b.UserName,
sum(cast(CallDuration as float)) as CallDuration,
COUNT(1) as Count,
sum(cast(BillValue as float)) as BillValue,
sum(cast(BillCost as float)) as BillCost
from CallOutCDR as a with (nolock)
left join SysUser as b with (nolock) on a.UserID = b.UserID
where";
                $group_sql = "group by a.UserID, b.UserName";
                break;
        }
        $params = [];
        if (!empty($this->userId)) {
            $where_sql .= " a.UserID = ?  and";
            $params[] = $this->userId;
        } else {
            $where_sql .= " a.UserID in (" . join(",", array_map(function ($v) {
                    return "'" . $v . "'";
                }, $this->session["current_sub_emp"])) . ") and";
        }
        if (!empty($this->callStartBillingDate)) {
            $where_sql .= " cast((a.CallStartBillingDate+' '+a.CallStartBillingTime) as datetime) < ?  and";
            $params[] = $this->callStopBillingDate . " " . ($this->callStopBillingTime ?? "23:59:59");
        }
        if (!empty($this->callStopBillingDate)) {
            $where_sql .= " cast((a.CallStopBillingDate+' '+a.CallStopBillingTime) as datetime) > ?  and";
            $params[] = $this->callStartBillingDate . " " . ($this->callStartBillingTime ?? "00:00:00");
        }
        $where_sql = substr($where_sql, 0, -5);//and 前面兩個空白，防止沒有參數進來，清除where五個字
        $sql = $select_sql . " " . $where_sql . " " . $group_sql . " order by UserID";
        $this->data = $dba->getAll($sql, $params);
        //echo $dba->mergeSQL($sql,$params)."^^";
    }

    public function getPointHistory()
    {
        $dba = $this->dba;
        $orderBy = " order by cast(AddTime as datetime) desc";//order by UserID asc
        $sql = "select 1 from RechargeLog where";
        $sql2 = "
select ROW_NUMBER() over ($orderBy) rownum,
LogID, UserID, AddValue, AddTime, SaveUserID, Memo
from RechargeLog with (nolock)
where";
        if (!empty($this->userId)) {
            $sql .= " UserID = ?  and";
            $sql2 .= " UserID = ?  and";
            $params[] = $this->userId;
        } else {
            $sql .= " UserID in (" . join(",", array_map(function ($v) {
                    return "'" . $v . "'";
                }, $this->session["current_sub_emp"])) . ") and";
            $sql2 .= " UserID in (" . join(",", array_map(function ($v) {
                    return "'" . $v . "'";
                }, $this->session["current_sub_emp"])) . ") and";
        }
        if (!empty($this->startDate)) {
            $sql .= " cast(AddTime as datetime) > ?  and";
            $sql2 .= " cast(AddTime as datetime) > ?  and";
            $params[] = $this->startDate . " " . "00:00:00";
        }
        if (!empty($this->endDate)) {
            $sql .= " cast(AddTime as datetime) < ?  and";
            $sql2 .= " cast(AddTime as datetime) < ?  and";
            $params[] = $this->endDate . " " . "23:59:59";
        }
        if (!empty($this->memo)) {
            $sql .= " Memo link '%?%'  and";
            $sql2 .= "  Memo link '%?%'  and";
            $params[] = $this->memo;
        }
        $sql = substr($sql, 0, -5);
        $sql2 = substr($sql2, 0, -5);
        $this->per_page = !empty($this->per_page) ? $this->per_page : 50;
        $this->page = !empty($this->page) ? $this->page : 0;
        $offset = $this->per_page * $this->page + 1;
        $limit = $this->per_page;
        $stmt = $dba->query($sql, $params);
        $this->rows = $dba->num_rows($stmt);
        $this->last_page = ceil($this->rows / $this->per_page);
//print_r($params);
        $this->data = $dba->getAllLimit($sql2, $params, $offset, $limit);
    }

    /**
     * @throws Exception
     * editRechargeLogMemo
     */
    public function editRechargeLogMemo()
    {
        $sql = "update RechargeLog set Memo=? where LogID=? ";
        $params = [
            $this->value,
            $this->id
        ];
        $res = $this->dba->exec($sql, $params);
        if ($res) {
            echo json_encode(["code" => 0]);
        } else {
            echo json_encode(["code" => -1]);
        }
    }

    public function getRecordDownload()
    {
        $emps = [$this->session["choice"]];
        $sub_emp = $this->getSubEmp($this->session["choice"]);
        foreach ($sub_emp as $user) {
            $emps[] = $user["UserID"];
        }
        $db = DB::table('CallOutCDR')->select('UserID', 'CallStartBillingDate', 'RecordFile');
        $db->whereBetween('cast(CallStartBillingDate as datetime)',
            [$this->callStartBillingDate, $this->callStopBillingDate]);
//        $db->andWhere('RecordFile', '<>', '');
        $db->addRaw("and RecordFile <> ''");
        $db->whereIn('UserID', $emps);
        if (!empty($this->extensionNo)) {
            $db->andWhere('ExtensionNo', $this->extensionNo);
        }
        if (!empty($this->orgCalledID)) {
            $db->andWhere('OrgCalledID', $this->orgCalledID);
        }
        if (!empty($this->callDuration)) {
            $db->andWhere('CallDuration', $this->durationCondition == "within" ? "<=" : ">", $this->callDuration);
        }
        $result = $db->get();
        if (!count($result)) {
            $this->warning = "條件範圍，找不到任何資料！！";//$dba->mergeSQL($sql,$params).
            return;
        }
        //include_once $this->base["comm_dir"]."phpzip.php";
        //$zip = new PHPZip("test.zip");
        //$tmpFolder = "tmp/".$this->session["choice"]."/";
        //@mkdir("tmp");
        //$this->delete_files($tmpFolder);
        //@mkdir($tmpFolder);
        //@mkdir("download");
        $this->targetFile = $this->session["choice"] . "RecordFile.zip";
        $zip = new ZipArchive();
        if ($zip->open($this->targetFile, ZIPARCHIVE::CREATE) !== true) {
            throw new \Exception("Cannot open <$this->targetFile>\n", 500);
        }
        $file_count = 0;
        foreach ($result as $data) {
            $userId = $data["UserID"];
            $connectDate = date("Ymd", strtotime($data["CallStartBillingDate"]));
            $fileName = $data["RecordFile"];
            $filePath = "D:\\Recording\\{$userId}\\{$connectDate}\\{$fileName}";
            if (file_exists($filePath) && !is_dir($filePath)) {
                //copy($filePath,$tmpFolder.$fileName);
                //$zip->addFile($tmpFolder.$fileName, basename($fileName));
                $zip->addFile($filePath, basename($fileName));
//				$zip->setCompressionIndex($file_count, ZipArchive::CM_STORE); // 不壓縮，但時間好像沒什麼差
                $file_count++;
            }
        }
        $zip->close();
        @mkdir($this->base['record']);
        $this->targetPath = $this->base['record'] . $this->targetFile;
        rename($this->base["root_folder"] . $this->targetFile, $this->targetPath);
    }

    public function getBlackList()
    {
        $dba = $this->dba;
        $page = $this->page = $this->page ?? 0;
        $per_page = $this->per_page = (!empty($this->per_page) ? $this->per_page : 100);
        $offset = $per_page * $page + 1;
        $limit = $per_page;
        $order = " order by OrderKey";
        $sql1 = " select count(1) as count from Blacklist where UserID = ?";
        $sql = "select ROW_NUMBER() over ({$order}) rownum,
                CalledNumber,OrderKey
                from Blacklist with (nolock)
                where UserID = ?";
        $params = [$this->session["choice"]];
        $result = $dba->getAll($sql1, $params)[0];
        $this->rows = $result["count"];
        $this->last_page = ceil($this->rows / $per_page);
        $this->data = $dba->getAllLimit($sql, $params, $offset, $limit);
        $this->writeCommunicationSearch($this->data);
    }

    public function getDownloadBlackList()
    {
        $sql = "select CalledNumber from BlackList with (nolock) where UserID=?";
        $params = [$this->session["choice"]];
        $this->createFile($sql, $params, "_BlackList");
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

    public function uploadBlackList()
    {
        $userID = $this->session["choice"];
        $this->result = [];
        $result = $this->readUploadList();
        $len = count($result);
        if (!$len) {
            return;
        } else {
            if ($len > $this->black_list_once_limit) {
                $this->warning = "筆數不得超過{$this->black_list_once_limit}筆";
                return;
            }
        }
        $chunk_contents = array_chunk(array_map(function ($n) {
            return "'" . $n . "'";
        }, $result), 300);
        foreach ($chunk_contents as $contents) {
            $tmp_content = join(',', $contents);
            $repeat_numbers = $this->dba->getAll("select CalledNumber from Blacklist with (nolock) where UserID='$userID' and CalledNumber in ({$tmp_content})");
            foreach ($repeat_numbers as $number) {
                $this->result[] = $number["CalledNumber"];
            }
        }
//        $sql = "";
//        $times = 0;
//        $sql_limit = 100;
//        $params = [];
//        while ($len-- > 0) {
//            $number = array_shift($result);
//            if (in_array($number, $this->result) || empty($number)) {
//                continue;
//            }
//            $sql .= "insert into Blacklist (UserID,CalledNumber) values (?,?);";
//            $params[] = $userID;
//            $params[] = $number;
//            if (++$times % $sql_limit == 0) {
//                $this->dba->exec($sql, $params);
//                $sql = "";
//                $params = [];
//            }
//        }
        $body = [];
        while ($len-- > 0) {
            $number = array_shift($result);
            if (in_array($number, $this->result) || empty($number)) {
                continue;
            }
            $body[] = [
                'UserID'       => $userID,
                'CalledNumber' => $number
            ];
        }
        $this->chunkInsertDB2('Blacklist', $body, 300);
        //$sql .= "update Blacklist set CalledNumber=REPLACE(CalledNumber,'?','') where CalledNumber like '?%';";
        //$sql .= "delete from Blacklist where CalledNumber like '?%';";
        //return false;
        //echo $sql;
        if (!empty($sql)) {
            $this->dba->exec($sql, $params);
        }
    }

    public function delBlackList()
    {
        $userID = $this->session["choice"];
        $delete = $this->delete;
        $delete = join(",", array_map(function ($v) {
            return "'" . $v . "'";
        }, $delete));
        $this->dba->exec("delete from Blacklist where UserID='$userID' and CalledNumber in ($delete)");
    }

    public function postBlackList()
    {
        $this->calledNumber = preg_replace('/\s(?=)/', '', $this->calledNumber);
        if (empty($this->calledNumber)) {
            return false;
        }
        $numbers = explode(",", $this->calledNumber);
        foreach ($numbers as $i => $number) {
            if (count($this->dba->getAll("select 1 from Blacklist with (nolock) where UserID='{$this->session['choice']}' and CalledNumber='$number'"))) {
                $this->result[] = $number;
                unset($numbers[$i]);
            }
        }
        if (count($numbers)) {
            $calledNumber = join(" , ", array_map(function ($v) {
                return "('" . $this->session["choice"] . "','$v')";
            }, $numbers));
            $sql = "insert into Blacklist (UserID,CalledNumber) values $calledNumber";
            $this->dba->exec($sql);
        }
    }

    public function effectiveNumberUpload()
    {
        //$userID = $this->session["choice"];
        $this->result = [];
        $result = $this->readUploadList();
        if (!$result) {
            return;
        }
        $len = count($result);
        if (!is_array($result) || !$len || empty($result)) {
            return;
        }
        $dba2 = new DBA();
        $dba2->dbHost = "125.227.84.247";
        $dba2->dbName = "NumberCollector";
        $dba2->connect();
        $sql = "";
        $times = 0;
        $sql_limit = 100;
        $params = [];
        $now = date("Y/m/d H:i:s", time());
        while ($len-- > 0) {
            $number = array_shift($result);
            if (in_array($number, $this->result) || empty($number)) {
                continue;
            }
//            $sql .= "insert into AllNumberList (CalledNumber, CallDateTime, CallResult) values (?, ?, ?);";
            $sql .= "insert into AllNumberList (CalledNumber, CallDateTime, CallResult) select '$number', '$now', '3' where not exists(select 1 from AllNumberList where CalledNumber=?) ;";
            $params[] = $number;
//            $params[] = $now;
//            $params[] = "3";
            if (++$times % $sql_limit == 0) {
                $dba2->exec($sql, $params);
                $sql = "";
                $params = [];
            }
        }
        if (!empty($sql)) {
            $dba2->exec($sql, $params);
        }
        $this->warning = "新增成功";
    }

    public function checkCalledNumber()
    {
        $result = $this->dba->getAll("select 1 from Blacklist with (nolock) where UserID=? and CalledNumber=?",
            [$this->session['choice'], $this->number]);
        echo json_encode([
            "status" => count($result),
            //"sql"=>$this->dba->mergeSQL("select 1 from Blacklist where UserID=? and CalledNumber=?",[$this->session[choice],$this->number])
        ]);
    }

    private function delete_files($target)
    {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
            foreach ($files as $file) {
                $this->delete_files($file);
            }
            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }
}

?>﻿
