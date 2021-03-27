<?php

use comm\DB;
use comm\SQL;

class MemberInfo_Model extends JModel
{
    public function getMemberList()
    {
        $this->page = (!empty($this->page) ? $this->page : 0);
        $limit = $this->per_page = (!empty($this->per_page)? $this->per_page: 100);
        $offset = $this->page * $this->per_page + 1;

        $sub_emps = [$this->session['choice']];
        $emps = $this->getSubEmp($this->session["choice"]);
        foreach($emps as $user)
        {
            $sub_emps[] = $user["UserID"];
        }

        $query = DB::table('MemberList')->select('ID', 'LastName', 'FirstName', 'Cellphone', 'Telephone', 'City', 'Address');
        $query2 = DB::table('MemberList')->select('count(1) as count');
        if(!empty($this->search))
        {
            $where = [
                ['LastName', 'like', "%$this->search%"],
                ['FirstName', 'like', "%$this->search%"],
                ['Cellphone', 'like', "%$this->search%"],
                ['Telephone', 'like', "%$this->search%"],
                ['City', 'like', "%$this->search%"],
                ['Address', 'like', "%$this->search%"]
            ];
            $query->where($where, SQL::OR);
            $query2->where($where, SQL::OR);
        }

        $this->data = $query->orderBy('ID')->limit($offset, $limit)->get();
        $this->total = $query2->get()[0]["count"];

        $this->last_page = ceil($this->total / $this->per_page);
    }

    public function getMemberDetail()
    {
        $this->data = DB::table('MemberList')
            ->select('ID', 'LastName', 'FirstName', 'Cellphone', 'Telephone', 'City', 'Address', 'UserID')
            ->where("ID", $this->ID)->get()[0];
    }

    public function getMemberCustom()
    {
        $this->subData = DB::table('MemberCustom')
            ->select('Name', 'Value')
            ->where("MemberID", $this->ID)->get();
    }

    public function deleteMemberList()
    {
        $ids = $this->ids;
        if (count($ids))
        {
            DB::table('MemberList')->delete()->whereIn('ID', $ids)->exec();
            DB::table('MemberCustom')->delete()->whereIn('MemberID', $ids)->exec();
        }
    }

    public function postMemberDetail()
    {
        $member_id = DB::table('MemberList')->insert([
            'LastName' => $this->LastName,
            'FirstName' => $this->FirstName,
            'Cellphone' => $this->Cellphone,
            'Telephone' => $this->Telephone,
            'City' => $this->City,
            'Address' => $this->Address,
            'UserID' => $this->session['choice']
        ])->insertID();
        $custInsert = [];
        foreach($this->CustomName as $index => $value)
        {
            $custInsert[] = [
                'MemberID' => $member_id,
                'Name' => $this->CustomName[$index],
                'Value' => $this->CustomValue[$index]
            ];
        }
        return DB::table('MemberCustom')->insert($custInsert)->exec();
    }

    public function updateMemberDetail()
    {
        $stmt0 = Db::table('MemberList')->update([
            'LastName' => $this->LastName,
            'FirstName' => $this->FirstName,
            'Cellphone' => $this->Cellphone,
            'Telephone' => $this->Telephone,
            'City' => $this->City,
            'Address' => $this->Address
        ])->where('ID', $this->ID)->exec();
        $stmt1 = DB::table('MemberCustom')->delete()->where('MemberID', $this->ID)->exec();
        $custInsert = [];
        foreach($this->CustomName as $index => $value)
        {
            $custInsert[] = [
                'MemberID' => $this->ID,
                'Name' => $this->CustomName[$index],
                'Value' => $this->CustomValue[$index]
            ];
        }
        $stmt2 = DB::table('MemberCustom')->insert($custInsert)->exec();
        return $stmt0 && $stmt1 && $stmt2;
    }
}

?>
