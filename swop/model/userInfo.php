<?php

use comm\DB;

class UserInfo_Model extends JModel
{
    /**
     * 使用者列表
     * @return $this
     */
    public function userList()
    {
        $emps = [$this->session["choice"]];
        $sub_emp = $this->getSubEmp($this->session["choice"]);
        foreach ($sub_emp as $user) {
            $emps[] = $user["UserID"];
        }
        $emps = join(",", array_map(function ($v) {
            return "'" . $v . "'";
        }, $emps));
        $dba = $this->dba;
        $sql = "select UserID,UseState,RateGroupID,Balance,UserName,Distributor,NoteText from SysUser where UserID in({$emps})";
        $this->sysUser = $dba->getAll($sql);
        foreach ($this->sysUser as $key => $user) {
            $count = $dba->getAll("select count(*) as count from CustomerLists as a WITH (NOLOCK) where UserID=?;",
                [$user["UserID"]])[0]["count"];
            $this->sysUser[$key]["ExtensionCount"] = $count;
        }
        return $this;
    }

    /**
     * 刪除使用者
     * @return $this
     */
    public function deleteUser()
    {
        $dba = $this->dba;
        $delete = "";
        foreach ($this->delete as $val) {
            $delete .= "?,";
            $params[] = $val;
        }
        $delete = substr($delete, 0, -1);
        $sql = "delete from SysUser where UserID in({$delete})";
        $dba->exec($sql, $params);
        return $this;
    }

    /**
     * 取得使用者單筆資訊
     * @return $this
     */
    public function getUserDetail($userId)
    {
        if (!isset($userId)) {
            return;
        }
        $dba = $this->dba;
        $sql = "select UseState,UserName,NoteText,RateGroupID,Balance,StartTime,StopTime,
                       CallWaitingTime,ParentID,MenuList,MaxRoutingCalls,MaxCalls,UserInfo,
                       Distributor,AutoStartTime,AutoStopTime,UserPassword,Overdraft,
                       SearchStartTime,SearchAutoStartTime,SearchAutoStopTime,SearchStopTime,
                       MaxSearchCalls,MaxRegularCalls,PermissionControl,UserID2, CanSwitchExtension
                from SysUser
                where UserID=?";
        $stmt = $dba->query($sql, [$userId]);
        return $dba->fetch_assoc($stmt);
    }

    /**
     * 取得費率群組
     * @return $this
     */
    public function getRateGroup()
    {
        $dba = $this->dba;
        $sql = "select RateGroupID,RateGroupName from RateGroup where UseState=?";
        $rateGroup = $dba->getAll($sql, ["1"]);
        uasort($rateGroup, function ($a, $b) {
            return (int)$a["RateGroupID"] == (int)$b["RateGroupID"] ? 0 :
                (int)$a["RateGroupID"] < (int)$b["RateGroupID"] ? -1 : 1;
        });
        return $rateGroup;
    }

    /**
     * 刪除費率群組
     * @return $this
     */
    public function delRateGroup()
    {
        $dba = $this->dba;
        $sql = "delete from RateGroup where RateGroupID in(?);
                delete from RateDetail where RateGroupID in(?)";
        $reatGroupIDs = join(",", $this->delete);
        $params = [$reatGroupIDs, $reatGroupIDs];
        $dba->exec($sql, $params);
        return $this;
    }

    /**
     * 取得費率群組詳細資料
     * @return $this
     */
    public function getRateGroupDetail($rateGroupId)
    {
        $dba = $this->dba;
        $sql = "select RateGroupName from RateGroup where RateGroupID=?";
        $stmt = $dba->query($sql, [$rateGroupId]);
        $result = $dba->fetch_assoc($stmt);
        $result["RateGroupID"] = $rateGroupId;
        return $result;
    }

    /**
     * 新增費率
     * @return $this
     */
    public function postRates()
    {
        $dba = $this->dba;
        $sql = "
            insert into RateGroup (RateGroupID,RateGroupName) values(?,?)
        ";
        $rs = $dba->exec($sql, [
            $this->rateGroupId,
            $this->rateGroupName
        ]);
        return $rs;
    }

    /**
     * 取得使用者費率
     * @return $this
     */
    public function getUserRates()
    {
        $dba = $this->dba;
        $rateGroupId = $this->rateGroupId;
        $sql = "
            select PrefixCode,Time1,RateValue1,RateCost1,Time2,RateValue2,RateCost2
            from RateDetail
            where RateGroupID = ?
        ";//,SubNum,AddPrefix
        $this->rateDetail = $dba->getAll($sql, [$rateGroupId]);
        return $this;
    }

    public function deleteUserRates()
    {
        $dba = $this->dba;
        $sql = "delete from RateDetail where PrefixCode=? and RateGroupID=?";
        $params = [$this->PrefixCode, $this->rateGroupId];
        $dba->exec($sql, $params);
        return $this;
    }

    public function deleteAllUserRates()
    {
        $dba = $this->dba;
        $rateGroupId = $this->rateGroupId;
        $sql = "delete from RateDetail where RategroupID=?";
        $dba->exec($sql, [$rateGroupId]);
        $sql = "delete from RateGroup where RateGroupID=?";
        $dba->exec($sql, [$rateGroupId]);
        return $this;
    }

    public function postUserRates()
    {
        $dba = $this->dba;
        $sql = "select 1 from RateDetail where RateGroupID=? and PrefixCode=?;";
        $params = [
            $this->rateGroupId,
            $this->PrefixCode
        ];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "座席與前置碼重複，無法新增。";
            return;
        }
        $sql = "
            insert into RateDetail
            (RateGroupID,PrefixCode,Time1,RateValue1,RateCost1,Time2,RateValue2,RateCost2)
            values(?,?,?,?,?,?,?,?)
        ";//,SubNum,AddPrefix
        $params = [
            $this->rateGroupId,
            $this->PrefixCode,
            $this->Time1 ?? 0,
            $this->RateValue1 ?? 0,
            $this->RateCost1 ?? 0,
            $this->Time2 ?? 0,
            $this->RateValue2 ?? 0,
            $this->RateCost2 ?? 0
        ];
//        ,
//        $this->SubNum??0,
//            $this->AddPrefix
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function updateUserRates()
    {
        $dba = $this->dba;
        $sql = "
            update RateDetail set
            Time1=?,
            RateValue1=?,
            RateCost1=?,
            Time2=?,
            RateValue2=?,
            RateCost2=?
            where
            RateGroupID=? and
            PrefixCode=?
        ";
//        ,
//        SubNum=?,
//            AddPrefix=?
        $params = [
            $this->Time1 ?? 0,
            $this->RateValue1 ?? 0,
            $this->RateCost1 ?? 0,
            $this->Time2 ?? 0,
            $this->RateValue2 ?? 0,
            $this->RateCost2 ?? 0,
//            $this->SubNum??0,
//            $this->AddPrefix,
            $this->rateGroupId,
            $this->PrefixCode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    //////////////////////////////////////////////////////////////// userRoute start

    /**
     * 取得使用者路由
     * @return $this
     */
    public function getUserRoute()
    {
        $dba = $this->dba;
        $sql = "
            select UserID,PrefixCode,AddPrefix,RouteCLI,TrunkIP,TrunkPort,RouteName,SubNum
            from AsRouting
        ";
        if ($this->session["choice"] != "root") {
            $sql .= " where UserID='{$this->session['choice']}'";
        }
        $this->userRoute = $dba->getAll($sql);
        return $this;
    }

    public function postUserRoute()
    {
        $dba = $this->dba;
        $sql = "select 1 from AsRouting where UserID=? and PrefixCode=?;";
        $params = [
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "用戶與前置碼重複，無法新增。";
            return;
        }
        $sql = "
            insert into AsRouting
            (UserID,PrefixCode,AddPrefix,
            RouteCLI,TrunkIP,TrunkPort,
            RouteName,SubNum)
            values
            (?,?,?,
            ?,?,?,
            ?,?)
        ";
        $params = [
            $this->UserID,
            $this->PrefixCode,
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function updateUserRoute()
    {
        $dba = $this->dba;
        $sql = "
            update AsRouting
            set
              AddPrefix=?,
              RouteCLI=?,
              TrunkIP=?,
              TrunkPort=?,
              RouteName=?,
              SubNum=?
            where
              UserID=? and
              PrefixCode=?
        ";
        $params = [
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum,
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function deleteUserRoute()
    {
        $dba = $this->dba;
        $sql = "delete from AsRouting where UserID=? and PrefixCode=?";
        $params = [$this->UserID, $this->PrefixCode];
        $dba->exec($sql, $params);
        return $this;
    }
    //////////////////////////////////////////////////////////////// userRoute end
    /////////////////////////////////////////////////////////////// manualUserRoute start
    public function getManualUserRoute()
    {
        $dba = $this->dba;
        $sql = "
            select UserID,PrefixCode,AddPrefix,RouteCLI,TrunkIP,TrunkPort,RouteName,SubNum
            from UserRouting
        ";
        if ($this->session["choice"] != "root") {
            $sql .= " where UserID='{$this->session['choice']}'";
        }
        $this->userRoute = $dba->getAll($sql);
        return $this;
    }

    public function postManualUserRoute()
    {
        $dba = $this->dba;
        $sql = "select 1 from UserRouting where UserID=? and PrefixCode=?;";
        $params = [
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "用戶與前置碼重複，無法新增。";
            return;
        }
        $sql = "
            insert into UserRouting
            (UserID,PrefixCode,AddPrefix,
            RouteCLI,TrunkIP,TrunkPort,
            RouteName,SubNum)
            values
            (?,?,?,
            ?,?,?,
            ?,?)
        ";
        $params = [
            $this->UserID,
            $this->PrefixCode,
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function updateManualUserRoute()
    {
        $dba = $this->dba;
        $sql = "
            update UserRouting
            set
              AddPrefix=?,
              RouteCLI=?,
              TrunkIP=?,
              TrunkPort=?,
              RouteName=?,
              SubNum=?
            where
              UserID=? and
              PrefixCode=?
        ";
        $params = [
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum,
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function deleteManualUserRoute()
    {
        $dba = $this->dba;
        $sql = "delete from UserRouting where UserID=? and PrefixCode=?";
        $params = [$this->UserID, $this->PrefixCode];
        $dba->exec($sql, $params);
        return $this;
    }
    /////////////////////////////////////////////////////////////// manualUserRoute end
    //////////////////////////////////////////////////////////////// searchRoute start
    /**
     * 取得查詢路由
     * @return \comm\DBA
     */
    public function getSearchRoute()
    {
        $dba = $this->dba;
        $sql = "
            select UserID,PrefixCode,AddPrefix,RouteCLI,TrunkIP,TrunkPort,RouteName,SubNum,InspectMode
            from SearchRouting
        ";
        if ($this->session["choice"] != "root") {
            $sql .= " where UserID='{$this->session['choice']}'";
        }
        $this->userRoute = $dba->getAll($sql);
        return $this;
    }

    /**
     * 新增查詢路由
     * @return \comm\DBA
     */
    public function postSearchRoute()
    {
        $dba = $this->dba;
        $sql = "select 1 from SearchRouting where UserID=? and PrefixCode=?;";
        $params = [
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "用戶與前置碼重複，無法新增。";
            return;
        }
        $sql = "
            insert into SearchRouting
            (UserID,PrefixCode,AddPrefix,
            RouteCLI,TrunkIP,TrunkPort,
            RouteName,SubNum,InspectMode)
            values
            (?,?,?,
            ?,?,?,
            ?,?,?)
        ";
        $params = [
            $this->UserID,
            $this->PrefixCode,
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum,
            $this->InspectMode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    /**
     * 更新查詢路由
     * @return \comm\DBA
     */
    public function updateSearchRoute()
    {
        $dba = $this->dba;
        $sql = "
            update SearchRouting
            set
              AddPrefix=?,
              RouteCLI=?,
              TrunkIP=?,
              TrunkPort=?,
              RouteName=?,
              SubNum=?,
              InspectMode=?
            where
              UserID=? and
              PrefixCode=?
        ";
        $params = [
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum,
            $this->InspectMode,
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    /**
     * 刪除查詢路由
     * @return \comm\DBA
     */
    public function deleteSearchRoute()
    {
        $dba = $this->dba;
        $sql = "delete from SearchRouting where UserID=? and PrefixCode=?";
        $params = [$this->UserID, $this->PrefixCode];
        $dba->exec($sql, $params);
        return $this;
    }
    //////////////////////////////////////////////////////////////// userRoute end

    /**
     * 新增使用者
     * @return bool
     */
    public function postUserDetail()
    {
        $dba = $this->dba;
        $sql = "select 1 from SysUser where UserID=? ;";
        $params = [$this->userID1];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "登入帳號已存在，無法新增。";
            return;
        }
        $sql = "
            insert into SysUser (
              UserID,    UseState,    UserName,
              NoteText,  RateGroupID, Balance,
              StartTime, StopTime,    CallWaitingTime,
              ParentID,  MenuList,    MaxRoutingCalls,
              MaxCalls,  UserInfo,    Distributor,
              AutoStartTime, AutoStopTime, Overdraft,
              SearchStartTime, SearchAutoStartTime, SearchAutoStopTime,
              SearchStopTime, MaxSearchCalls, MaxRegularCalls,
              PermissionControl, UserID2, CanSwitchExtension
              ) values(
              ?,?,?,
              ?,?,?,
              ?,?,?,
              ?,?,?,
              ?,?,?,
              ?,?,?,
              ?,?,?,
              ?,?,?,
              ?,?,?
              );
        ";
        $params = [
            $this->userID1,
            $this->useState ? 1 : 0,
            $this->userName,
            $this->noteText,
            $this->rateGroupID,
            $this->balance,
            $this->startTime,
            $this->stopTime,
            $this->callWaitingTime,
            $this->parentId,
            join(",", $this->menuList),
            $this->maxRoutingCalls,
            $this->maxCalls,
            $this->userInfo,
            $this->distributor,
            $this->autoStartTime,
            $this->autoStopTime,
            $this->overdraft,
            $this->searchStartTime,
            $this->searchAutoStartTime,
            $this->searchAutoStopTime,
            $this->searchStopTime,
            $this->maxSearchCalls,
            $this->maxRegularCalls,
            $this->permissionControl ? 1 : 0,
            $this->userID2,
            $this->canSwitchExtension ? 1 : 0,
        ];
        if ($this->balance && $this->balance != 0) {
            $sql .= "insert into RechargeLog (UserID,AddValue,AddTime,SaveUserID)
                      values (?,?,?,?);";
            $params = array_merge($params, [
                $this->userID1,
                $this->balance,
                date("Y-m-d H:i:s", time()),
                $this->session["choice"]
            ]);
        }
        $result = $dba->exec($sql, $params);
//        var_dump($sql);
//        print_r($params);
        return $result;
    }

    /**
     * 更新用戶資料
     * @return array
     */
    public function updateUserDetail()
    {
        $dba = $this->dba;
        if ($this->session["isRoot"]) {
            $sql = "
            update SysUser set
              UseState = ?,
              UserName = ?,
              NoteText = ?,
              RateGroupID = ?,
              Balance += ?,
              StartTime = ?,
              StopTime = ?,
              CallWaitingTime = ?,
              ParentID=?,
              MenuList=?,
              MaxRoutingCalls=?,
              MaxCalls=?,
              UserInfo=?,
              Distributor=?,
              AutoStartTime=?,
              AutoStopTime=?,
              Overdraft=?,
              SearchStartTime=?,
              SearchAutoStartTime=?,
              SearchAutoStopTime=?,
              SearchStopTime=?,
              MaxSearchCalls=?,
              MaxRegularCalls=?,
              PermissionControl=?,
              UserID2=?,
              CanSwitchExtension=?
            where UserId = ?;
        ";
            $permission = join(",", $this->menuList);
            $params = [
                $this->useState ? 1 : 0,
                $this->userName,
                $this->noteText,
                $this->rateGroupID,
                $this->balance,
                $this->startTime,
                $this->stopTime,
                $this->callWaitingTime,
                $this->parentId,
                $permission,
                $this->maxRoutingCalls,
                $this->maxCalls,
                $this->userInfo,
                $this->distributor,
                $this->autoStartTime,
                $this->autoStopTime,
                $this->overdraft,
                $this->searchStartTime,
                $this->searchAutoStartTime,
                $this->searchAutoStopTime,
                $this->searchStopTime,
                $this->maxSearchCalls,
                $this->maxRegularCalls,
                $this->permissionControl ? 1 : 0,
                $this->userID2,
                $this->canSwitchExtension ? 1 : 0,
                $this->userId
            ];
            $this->setUpdatePermission($this->userId, $permission, $this->permissionControl ? 1 : 0);
            if ($this->balance && $this->balance != 0) {
                $sql .= "insert into RechargeLog (UserID,AddValue,AddTime,SaveUserID)
                      values (?,?,?,?);";
                $params = array_merge($params, [
                    $this->userId,
                    $this->balance,
                    date("Y-m-d H:i:s", time()),
                    $this->session["choice"]
                ]);
            }
        } else {
            $sql = "
            update SysUser set
              MenuList=?
            where UserId = ?;
        ";
            $permission = join(",", $this->menuList);
            $params = [
                $permission,
                $this->userId
            ];
        }
        $result = $dba->exec($sql, $params);
        return $result;
    }

    private function setUpdatePermission($userId, $permission, $permission_control)
    {
        if ($this->session["choice"] == $userId) {
            $this->session["permission"] = $permission;
            $this->session["permission_control"] = $permission_control;
        }
        if ($this->session["login"]["UserID"] == $userId) {
            $this->session["login"]["MenuList"] = $permission;
            $this->session["login"]["PermissionControl"] = $permission_control;
            return;
        }
        foreach ($this->session["sub_emp"] as $key => $sub_emp) {
            if ($sub_emp["UserID"] == $userId) {
                $this->session["sub_emp"][$key]["MenuList"] = $permission;
                $this->session["sub_emp"][$key]["PermissionControl"] = $permission_control;
                return;
            }
        }
    }

    /**
     * 取得路由查詢
     */
    public function getRouteSearch()
    {
        $dba = $this->dba;
        $extensionNo = $this->extensionNo;
        $len = strlen($extensionNo);
        $prefixCode_condition = [];
        if ($len == 0) {
            return;
        }
        while ($len) {
            $prefixCode_condition[] = "?";
            $prefixCode_params[] = substr($extensionNo, 0, $len--);
        }
        $sql = "
          select PrefixCode from RateDetail where PrefixCode = SUBSTRING(?,0,
            (

            select max(len(PrefixCode))+1
            from RateDetail
            where RateGroupID in ( select RateGroupID from SysUser where UserID=? ) and
                  PrefixCode in (" . join(",", $prefixCode_condition) . ")

            )
          )";
        $params = [];
        $params[] = $extensionNo;
        $params[] = $this->userId;
        $params = array_merge($params, $prefixCode_params);
        $result = $dba->getAll($sql, $params);
        if (count($result)) {
            $data = $result[0];
            $this->prefixCode = $data["PrefixCode"];
        }
        if ($this->prefixCode) {
            $this->data = 1;
            $sql = "
              select RouteCLI, AddPrefix, TrunkIP, PrefixCode, SubNum from AsRouting
              where UserID=? and
              (
                  PrefixCode = SUBSTRING(?,0,
                  (
                    select max(len(PrefixCode))+1
                    from AsRouting
                    where UserID=? and PrefixCode in (" . join(",", $prefixCode_condition) . "))
                  ) or PrefixCode = '*'
              );";
            $params = array_merge([$this->userId, $extensionNo, $this->userId], $prefixCode_params);
            $result = $dba->getAll($sql, $params);
            $data = $result[0];
            if (count($result) > 1) {
                if ($result[0]["PrefixCode"] == "*") {
                    $data = $result[1];
                }
            }
            $this->routeAddPrefix = $data["PrefixCode"];
            $this->mainCall = $data["RouteCLI"];
            $this->lastCalled = $data["AddPrefix"] . substr($extensionNo, $data["SubNum"]);
            $this->trunkIp = $data["TrunkIP"];
        }
    }

    public function getBalance()
    {
        $users = explode(",", $this->users);
        if (!count($users)) {
            return;
        }
        $where_sql = "";
        foreach ($users as $user) {
            $where_sql .= " UserID='{$user}' or ";
        }
        $where_sql = substr($where_sql, 0, -3);
        $sql = "select Balance from SysUser WITH (NOLOCK) where $where_sql";
        $result = $this->dba->getAll($sql);
        $balances = [];
        foreach ($result as $val) {
            $balances[] = $val["Balance"];
        }
        echo json_encode($balances);
    }

    public function setGroupName()
    {
        DB::table('RateGroup')
            ->update([
                "RateGroupName" => $this->RateGroupName
            ])
            ->where("RateGroupID", $this->RateGroupID)
            ->exec();
    }
}

?>
