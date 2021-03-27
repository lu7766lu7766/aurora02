<?php

use comm\DB;

class Index_Controller extends JController
{
    public function index()
    {
        return parent::render();
    }

    public function service()
    {
        return parent::render();
    }

    public function password()
    {
        return parent::render();
    }

    public function logout()
    {
        $this->model->session = null;
        return parent::redirect();
    }

    public function chgUser()
    {
        $user = DB::table("SysUser")->select("*")->where("UserID", $this->model->choiceUser)->get()[0];
        $this->model->session["choicer"] = $user;
        $this->model->session["choice"] = $this->model->choiceUser;
        $this->model->session["isRoot"] = $this->model->session["choice"] == "root";
        $this->model->choice = $this->model->choiceUser;
        $this->model->session["permission"] = $this->model->choicePermission;
        $this->model->permission = $this->model->choicePermission;
        $this->model->session["current_sub_emp"] = EmpHelper::getCurrentSubEmp($this->model->session["sub_emp"],
            $this->model->session["choice"]);
        $this->model->session["permission_control"] =
            $this->model->session["choice"] == $this->model->session["login"]["UserID"] ? $this->model->session["login"]["PermissionControl"] :
                EmpHelper::getPermissionControl($this->model->session["sub_emp"], $this->model->session["choice"]);
        //echo $this->menu->CreateMenu($this->model->permission);
    }

    public function update_pwd()
    {
        if ($this->model->result) {
            echo "success";
        } else {
            echo "error";
        }
    }

    /**
     * 關機
     */
    public function shotdown()
    {
        $url = "http://127.0.0.1:60/SHUTDOWN.atp";
        comm\Http::get($url);
        $this->redirect("main/main");
    }

    /**
     * 重啟
     */
    public function reboot()
    {
        $url = "http://127.0.0.1:60/REBOOT.atp";
        comm\Http::get($url);
        $this->redirect("main/main");
    }

    public function test()
    {
//        echo "<pre>";
//        print_r($this->model->data);
    }
}

?>
