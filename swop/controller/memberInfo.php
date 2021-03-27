<?php

class MemberInfo_Controller extends JController
{
    public function memberList()
    {
        if ($this->model->status == 'delete' && is_array($this->model->delete) && count($this->model->delete)) {
            $post["ids"] = $this->model->delete;
            $this->redirect("/memberInfo/memberDel", $post);
        }
        $this->model->getMemberList();
        $this->model->pageSelect = PageHelper::getPageSelect($this->model->page, $this->model->last_page);
        parent::render();
    }

    public function memberDel()
    {
        $this->model->deleteMemberList();
        $this->redirect("/memberInfo/memberList");
    }

    public function memberAdd()
    {
        $this->menu->currentName = "會員新增";
        if ($this->model->submit) {
            $result = $this->model->postMemberDetail();
            if ($result) {
                $this->redirect("/memberInfo/memberList");
            }
        }
        parent::render('memberDetail');
    }

    public function memberModify()
    {
        $this->menu->currentName = "會員修改";
        if ($this->model->submit) {
            $result = $this->model->updateMemberDetail();
            if ($result) {
                $this->redirect("/memberInfo/memberList");
            }
        }
        $this->model->getMemberDetail();
        $this->model->getMemberCustom();
        parent::render('memberDetail');
    }
}

?>
