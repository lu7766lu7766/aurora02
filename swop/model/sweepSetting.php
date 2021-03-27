<?php

class SweepSetting_Model extends JModel
{
	public function postAddSweep ()
	{
		$dba = $this->dba;

		$max_limit = 200000;

		$result = [ ];
		$len = 0;
		if ($this->numberMode == "1") {
			$result = $this->readUploadList();

			$len = count($result);
			if (!$len) {
				$this->warning = "筆數異常，請重新檢查檔案";

				return;
			}

			if (!count($result[ 0 ])) {
				$this->warning = "起始電話異常，請重新檢查檔案";

				return;
			}

			$this->startCalledNumber = $result[ 0 ];
			$this->calledCount = $len;

			if ($len > $max_limit) {
				$this->warning = "筆數不得超過20萬筆";

				return;
			}
		}

		if ($this->calledCount > $max_limit) {
			$this->warning = "筆數不得超過20萬筆";

			return;
		}

		$callOutID = $dba->getAll("select max(CallOutID)+1 as count from SearchPlan;", [ ])[ 0 ][ "count" ];//確保寫入SearchNumberList的calloutid是唯一值
		$callOutID = empty( $callOutID ) ? "1" : $callOutID;

		$sql = "
          insert into SearchPlan (UserID,         PlanName,         StartCalledNumber,
                                CalledCount,      CallProgressTime, StartDateTime,
                                UseState,         ConcurrentCalls,
                                NumberMode,       CallOutID)
          values(?, ?, ?,
                 ?, ?, ?,
                 ?, ?,
                 ?, ?);
        ";//
		$params = [
			$this->session[ "choice" ], $this->planName, $this->startCalledNumber,
			$this->calledCount, $this->callProgressTime, $this->startDateTime,
			0,/*$this->useState??*/
			$this->concurrentCalls,
			$this->numberMode, $callOutID
		];

		$phone_len = strlen($this->startCalledNumber);

		$times = 0;
		$sql_limit = 100;

		switch ($this->numberMode) {
			case "0"://range
				while ($this->calledCount-- > 0) {
					$sql .= "insert into SearchNumberList (CallOutID,CalledNumber) values(?,?);";
					$params[] = $callOutID;
					$params[] = str_pad($this->startCalledNumber++, $phone_len, '0', STR_PAD_LEFT);
					if ($times++ % $sql_limit == 0) {
//                        echo $sql."^^";
//                        print_r($params);
						$dba->exec($sql, $params);
						$sql = "";
						$params = [ ];
					}
				}
				break;
			case "1"://list
				while ($len-- > 0) {
					$sql .= "insert into SearchNumberList (CallOutID,CalledNumber) values(?,?);";
					$params[] = $callOutID;
					$params[] = array_shift($result);
					if ($times++ % $sql_limit == 0) {
						$dba->exec($sql, $params);
						$sql = "";
						$params = [ ];
					}
				}
				break;
		}


		if (!empty( $sql )) {
			$dba->exec($sql, $params);
		}

		return $this;
	}

	public function getAddSweep ()
	{
		$dba = $this->dba;
		$this->userId = $this->session[ "choice" ];
//        $sql = "select a.UserName,a.Balance,a.CallWaitingTime,b.CallProgressTime,b.CallOutID,
//                from SysUser as a WITH (NOLOCK)
//                left join SearchPlan as b WITH (NOLOCK) on a.UserID=b.UserID where a.UserID=?";
		$sql = "select UserID,CallOutID,PlanName,StartCalledNumber,CalledCount,UseState
                from SearchPlan WITH (NOLOCK) where UserID=? order by CallOutID desc";
		$params = [
			$this->userId
		];
		$this->data = $dba->getAll($sql, $params);
	}

	public function deleteAddSweep ()
	{
		if (!is_array($this->delete)) {
			return;
		}
		$sql = "delete from SearchPlan where";
		$sql2 = "";
		$params = [ ];
		$params2 = [ ];
		foreach ($this->delete as $delete) {
			list( $userId, $callOutId ) = explode(",", $delete);
			$sql .= "(UserID=?  and CallOutID=?) or";
			$params[] = $userId;
			$params[] = $callOutId;
			$sql2 .= "delete from SearchNumberList where CallOutID=?;";
			$params2[] = $callOutId;
		}
		$sql = substr($sql, 0, -3);
		$sql .= ( ";" . $sql2 );
		$params = array_merge($params, $params2);

		$this->dba->exec($sql, $params);
	}

	public function getAddSweepDetail ()
	{
		$dba = $this->dba;
		$sql = "select b.UserName, b.Balance,
                       a.CallProgressTime, a.UseState, a.PlanName,
                       a.StartCalledNumber, a.CalledCount, a.ConcurrentCalls,
                       a.StartDateTime, a.TotalFee
                from SearchPlan as a WITH (NOLOCK)
                left join SysUser as b WITH (NOLOCK) on a.UserID=b.UserID
                where a.UserID=? and a.CallOutID=?";
		$params = [
			$this->userId,
			$this->callOutId
		];
		//, b.CallWaitingTime, a.NumberMode
		$this->data = $dba->getAll($sql, $params)[ 0 ];
	}

	public function updateAddSweepDetail ()
	{
		$dba = $this->dba;//update SysUser set CallWaitingTime=? where UserID=?;
		$sql = "
            update SearchPlan set
              PlanName=?,
              StartCalledNumber=?,
              CalledCount=?,
              CallProgressTime=?,
              ConcurrentCalls=?,
              StartDateTime=?,
              UseState=?
            where UserID=? and CallOutID=?;
        ";
		$params = [
			//$this->callWaitingTime, $this->userId,
			$this->planName,
			$this->startCalledNumber,
			$this->calledCount,
			$this->callProgressTime,
			$this->concurrentCalls,
			$this->startDateTime,
			$this->useState??0,
			$this->userId, $this->callOutId
		];

		if ($this->calledCount != $this->calledCount_source) {
			$sql .= "delete from  SearchNumberList where CallOutID=?;";
			$params[] = $this->callOutId;

			$phone_len = strlen($this->startCalledNumber);

			switch ($this->numberMode) {
				case "0"://range
					while ($this->calledCount-- > 0) {
						$sql .= "insert into SearchNumberList (CallOutID,CalledNumber) values(?,?);";
						$params[] = $this->callOutId;
						$params[] = str_pad($this->startCalledNumber++, $phone_len, '0', STR_PAD_LEFT);
					}
					break;
			}

		}

		return $dba->exec($sql, $params);
	}
}

?>
