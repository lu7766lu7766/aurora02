<?php
//require_once "swop/library/dba.php";
//$dba=new dba();
use \comm\DB;
use \comm\SQL;

class ExtensionInfo_Model extends JModel
{
	/**
	 * 新增分機
	 * @return $this|void
	 */
	public function postExtension ()
	{
		$dba = $this->dba;
		$this->extensionNos = ( empty( $this->extensionNos ) || $this->extensionNos < $this->extensionNo ) ?
			$this->extensionNo : $this->extensionNos;

		$tmp_extensionNo = $this->extensionNo;
		$exists_extensionNo = [ ];

		$sql = "insert into CustomerLists (CustomerNO,UserID,ExtensionNo,UserName,ExtName,OffNetCli,CustomerPwd) values ";

		$params = [ ];

		while ($tmp_extensionNo <= $this->extensionNos) {
			$exists_extensionNo[] = "'$tmp_extensionNo'";

			$sql .= "(?,?,?,?,?,?,?),";
			$params[] = $tmp_extensionNo;
			$params[] = $this->userId;
			$params[] = $tmp_extensionNo;
			$params[] = $tmp_extensionNo++;
			$params[] = $this->extName;
			$params[] = $this->offNetCli;
			$params[] = $this->customerPwd;
		}
		$sql = substr($sql, 0, -1);

		//////////////////////////////////
		$sql1 = "select CustomerNO from CustomerLists where CustomerNO in (" . join(",", $exists_extensionNo) . ")";
		$result = $dba->getAll($sql1);
		$exists_extensionNo = [ ];
		foreach ($result as $val)
			$exists_extensionNo[] = $val[ "CustomerNO" ];

		if (count($result) > 0) {
			$this->warning = join(",", $exists_extensionNo) . "分機已存在，請避開這些分機";

			return;
		}
		/////////////////////////////////

		$result = $dba->exec($sql, $params);

		$tmp_extensionNo = $this->extensionNo;
		$sql = "
            insert into ExtensionGroup (UserID,CalloutGroupID,CustomerNO)
            values
        ";
		$params = [ ];
		while ($tmp_extensionNo <= $this->extensionNos) {
			$sql .= "(?,?,?),";
			$params[] = $this->userId;
			$params[] = $this->calloutGroupId;
			$params[] = $tmp_extensionNo++;
		}
		$sql = substr($sql, 0, -1);
		$result2 = $dba->exec($sql, $params);

		return $result && $result2;
	}

	/**
	 * 刪除分機
	 * @return $this|void
	 */
	public function deleteExtension ()
	{
		$dba = $this->dba;
		$sql = "";
		$params = [ ];
		foreach ($this->delete as $delete) {
			$params = array_merge($params, explode(",", $delete), explode(",", $delete));
			$sql .= "
                delete from ExtensionGroup
                where
                UserID=? and
                CustomerNO=?;
                delete from CustomerLists
                where
                UserID=? and
                CustomerNO=?;
            ";
		}
		$result = $dba->exec($sql, $params);

		return $result;
	}

	/**
	 * 分機管理
	 * @return $this|void
	 */
	public function getExtensionManage ()
	{
		$emps = [ $this->session[ "choice" ] ];
		$sub_emp = $this->getSubEmp($this->session[ "choice" ]);
		foreach ($sub_emp as $user) {
			$emps[] = $user[ "UserID" ];
		}

		$page = $this->page = $this->page ?? 0;
		$per_page = $this->per_page = $this->per_page ?? 50;
		$offset = $per_page * $page + 1;
		$limit = $per_page;

		$db = DB::table('CustomerLists')->select('count(1) as count')->whereIn('UserID', $emps);
		$db2 = Db::table('CustomerLists as a')->select([
			'a.UserID',
			'a.ExtName',
			'a.ExtensionNo',
			'c.HostInfo',
			'a.StartRecorder',
			'b.CalloutGroupID',
			'a.Suspend',
			'c.ETime',
			'c.PingTime',
			'c.Received',
			'a.UseState',
			'a.OffNetCli'
		])->leftJoin('ExtensionGroup as b', 'a.UserID=b.UserID', 'and', 'a.ExtensionNo=b.CustomerNO')
			->leftJoin('RegisteredLogs as c', 'a.ExtensionNo=c.CustomerNO')
			->whereIn('a.UserID', $emps);

		if (!empty( $this->search_userID )) {
			$db->andWhere('UserID', $this->search_userID);
			$db2->andWhere('a.UserID', $this->search_userID);
		}

		if (!empty( $this->search_content )) {
			$db->andWhere(function ($mdb) {
				return $mdb->where([
					[ 'UserID', $this->search_content ],
					[ 'ExtensionNo', $this->search_content ]
				], SQL:: OR);
			});

			$db2->andWhere(function ($mdb) {
				return $mdb->where([
					[ 'a.UserID', $this->search_content ],
					[ 'a.ExtensionNo', $this->search_content ]
				], SQL:: OR);
			});
		}

		$this->rows = $db->get()[ 0 ][ 'count' ];
		$this->last_page = ceil($this->rows / $per_page);
		$db2->orderBy('a.UserID')->limit($offset, $limit);
		$this->data = $db2->get();
	}

	/**
	 * 分機修改
	 * @return $this|void
	 */
	public function getExtensionDetail ()
	{
		$dba = $this->dba;
		$userId = $this->userId;
		$extensionNo = $this->extensionNo;
		$sql = "
            select a.ExtName,a.CustomerPwd,a.StartRecorder,a.Suspend,a.UseState,b.CalloutGroupID,a.OffNetCli
            from CustomerLists as a WITH (NOLOCK)
            left join ExtensionGroup as b WITH (NOLOCK) on a.UserID=b.UserID and a.CustomerNO=b.CustomerNO
            where a.UserID=? and a.ExtensionNo=?
        ";
		$params = [ $userId, $extensionNo ];
		$stmt = $dba->query($sql, $params);
		$this->data = $dba->fetch_assoc($stmt);
	}

	/**
	 * 分機更新
	 * @return $this|void
	 */
	public function updateExtensionDetail ()
	{
		$dba = $this->dba;
		$userId = $this->userId;
		$extensionNo = $this->extensionNo;

//        if($this->session["choice"]!="root"){
//            $sql = "select UseState from CustomerLists WITH (NOLOCK) where UserID=? and ExtensionNo=?;";
//            $params = array($userId,$extensionNo);
//            $this->useState = $dba->getAll($sql,$params)[0]["UseState"];
//        }
		if ($this->session[ "choice" ] != "root") {
			$sql = "
                select OffNetCli
                from CustomerLists WITH (NOLOCK)
                where UserID=? and ExtensionNo=?
            ";
			$params = [ $userId, $extensionNo ];
			$this->offNetCli = $dba->getAll($sql, $params)[ 0 ][ OffNetCli ];
		}


		$sql = "
            update CustomerLists set
                ExtName=?,
                CustomerPwd=?,
                StartRecorder=?,
                Suspend=?,
                UseState=?,
                OffNetCli=?
            where UserID=? and ExtensionNo=?;
            update ExtensionGroup set
                CalloutGroupID=?
            where UserID=? and CustomerNO=?;
        ";
		$params = [
			$this->extName,
			$this->customerPwd,
			$this->startRecorder??0,
			$this->suspend??1,
			$this->useState??0,
			$this->offNetCli,

			$userId, $extensionNo,

			$this->calloutGroupId,

			$userId, $extensionNo
		];

		return $dba->exec($sql, $params);

	}

	/**
	 * 座息策略 (已屏蔽)
	 * @return $this|void
	 */
	public function getSeatTactics ()
	{
		$dba = $this->dba;

		$where = "where";

		if (!empty( $this->userId )) {
			$where .= " a.UserID=?  and";
			$params[] = $this->userId;
		}

		if (!empty( $this->calloutGroupId )) {
			$where .= " b.CalloutGroupID=?  and";
			$params[] = $this->calloutGroupId;
		}
		$where = substr($where, 0, -5);

		$sql = "select a.UserID,b.CalloutGroupID,count(1) as ExtensionRows
                from CustomerLists as a WITH (NOLOCK)
                left join ExtensionGroup as b WITH (NOLOCK) on a.UserID=b.UserID and a.ExtensionNo=b.CustomerNO
                $where
                group by a.UserID,b.CalloutGroupID";

		$this->data = $dba->getAll($sql, $params);
	}
}

?>
