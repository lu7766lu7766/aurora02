<?php

class UserInfo_Controller extends JController
{
    public function userList()
    {
        $model = $this->model;
        if (is_array($model->delete) && count($model->delete)) {
            $post["delete"] = $model->delete;
            $this->redirect("/userInfo/deleteUser", $post);
        }
        return parent::render();
    }

    public function deleteUser()
    {
        $this->redirect("/userInfo/userList");
    }

    public function addRates()
    {
        $model = $this->model;
        if ($model->submit)//更新
        {
            $result = $model->postRates();
            if ($result) {
                $this->redirect("userInfo/allRates");
            }
        }
        return parent::render();
    }

    public function allRates()
    {
        if ($this->model->submit) {
            $this->model->delRateGroup();
        }
        $this->model->rateGroup = $this->model->getRateGroup();
        return parent::render();
    }

    public function userRatesModify()
    {
        $model = $this->model;
        if ($model->submit)//更新
        {
            if ($model->status == "delete" && $model->PrefixCode != "") {
                $model->deleteUserRates();
            } else {
                if ($model->status == "deleteAll")//deleteAll
                {
                    $model->deleteAllUserRates();
                    $this->redirect("userInfo/allRates");
                } else {
                    if ($model->status == "update") {
                        $model->updateUserRates();
                    } else {
                        $model->postUserRates();
                    }
                }
            }
        }
        $model->getUserRates();
        $model->subNumSelect = ["name" => "SubNum", "type" => "select", "class" => "form-control"];
        $i = 0;
        while ($i < 10) {
            $model->subNumSelect["option"][] = ["value" => $i, "name" => $i++];
        }
        $this->menu->currentName = "( " . $model->rateGroupId . " <=> " . $model->rateGroupName . " )用戶費率表更改";
        return parent::render("userRatesModify2");
    }

    public function userRoute()
    {
        $model = $this->model;
        if ($model->submit)//更新
        {
//            if($model->status=="delete" && is_array($model->delete) && count($model->delete))
            if ($model->status == "delete") {
                $model->deleteUserRoute();
            } else {
                if ($model->status == "update") {
                    $model->updateUserRoute();
                } else {
                    $model->postUserRoute();
                }
            }
        }
        $model->empSelect2 = EmpHelper::getEmpSelect($model->empSelect,
            ["name" => "UserID", "attr" => ["type" => "select"], "option" => ["value" => "", "name" => ""]]);
        foreach ($model->empSelect2["option"] as $i => $option) {
            if ($option["value"] != "" &&
                $option["value"] != $model->session["choice"] &&
                !in_array($option["value"], $model->session["current_sub_emp"])) {
                unset($model->empSelect2["option"][$i]);
            }
        }
        sort($model->empSelect2["option"]);
        $model->getUserRoute();
        return parent::render("userRoute2");
    }

    public function manualUserRoute()
    {
        $model = $this->model;
        if ($model->submit)//更新
        {
//            if($model->status=="delete" && is_array($model->delete) && count($model->delete))
            if ($model->status == "delete") {
                $model->deleteManualUserRoute();
            } else {
                if ($model->status == "update") {
                    $model->updateManualUserRoute();
                } else {
                    $model->postManualUserRoute();
                }
            }
        }
        $model->empSelect2 = EmpHelper::getEmpSelect($model->empSelect,
            ["name" => "UserID", "attr" => ["type" => "select"], "option" => ["value" => "", "name" => ""]]);
        foreach ($model->empSelect2["option"] as $i => $option) {
            if ($option["value"] != "" &&
                $option["value"] != $model->session["choice"] &&
                !in_array($option["value"], $model->session["current_sub_emp"])) {
                unset($model->empSelect2["option"][$i]);
            }
        }
        sort($model->empSelect2["option"]);
        $model->getManualUserRoute();
        return parent::render("manualUserRoute2");
    }

    public function searchRoute()
    {
        $model = $this->model;
        if ($model->submit)//更新
        {
//            if($model->status=="delete" && is_array($model->delete) && count($model->delete))
            if ($model->status == "delete") {
                $model->deleteSearchRoute();
            } else {
                if ($model->status == "update") {
                    $model->updateSearchRoute();
                } else {
                    $model->postSearchRoute();
                }
            }
        }
        $model->empSelect2 = EmpHelper::getEmpSelect($model->empSelect,
            ["name" => "UserID", "attr" => ["type" => "select"], "option" => ["value" => "", "name" => ""]]);
        foreach ($model->empSelect2["option"] as $i => $option) {
            if ($option["value"] != "" &&
                $option["value"] != $model->session["choice"] &&
                !in_array($option["value"], $model->session["current_sub_emp"])) {
                unset($model->empSelect2["option"][$i]);
            }
        }
        sort($model->empSelect2["option"]);
        $model->inspectModeSelect = [
            "id"       => "InspectMode",
            "name"     => "InspectMode",
            "type"     => "select",
            "class"    => "form-control",
            "option"   => [],
            "selected" => $model->data["InspectMode"]
        ];
        $model->inspectModeSelect["option"][] = [
            "value" => 0,
            "name"  => "模式0"
        ];
        $model->inspectModeSelect["option"][] = [
            "value" => 1,
            "name"  => "模式1"
        ];
        $model->inspectModeSelect["option"][] = [
            "value" => 2,
            "name"  => "模式2"
        ];
        $model->getSearchRoute();
        return parent::render("searchRoute2");
    }

    public function userAdd()
    {
        $model = $this->model;
        $this->menu->currentName = "用戶新增";
        if ($model->submit) {
            $result = $model->postUserDetail();
            if ($result) {
                $this->redirect("userInfo/userList");
            }
        }
        $this->getDetailSelect();
        return parent::render("userDetail");
    }

    private function getDetailSelect()
    {
        $model = $this->model;
        $model->user = $model->getUserDetail($this->model->userId);
        $model->rateGroup = $model->getRateGroup();
        $model->rateGroupID = [
            "id"       => "rateGroupID",
            "name"     => "rateGroupID",
            "class"    => "form-control",
            "option"   => [],
            "selected" => $model->user["RateGroupID"]
        ];
        $model->rateGroupID["option"][] = ["value" => 0, "name" => ""];
        foreach ($model->rateGroup as $val) {
            $model->rateGroupID["option"][] = [
                "value" => $val["RateGroupID"],
                "name"  => $val["RateGroupID"] . " (" . $val["RateGroupName"] . ")"
            ];
        }
        $model->concurrentCalls = [
            "id"       => "concurrentCalls",
            "name"     => "concurrentCalls",
            "class"    => "form-control",
            "option"   => [],
            "selected" => $model->user["ConcurrentCalls"]
        ];
        $times = 10;
        while ($times) {
            $model->concurrentCalls["option"][] = [
                "value" => $times,
                "name"  => "每 1 秒 " . $times-- . " 通"
            ];
        }
        $model->empSelect2 = EmpHelper::KillValue(
            EmpHelper::getEmpSelect($model->empSelect,
                [
                    "name" => "parentId",
                    //"option"=>array("value"=>"","name"=>"")
                ]),
            $model->userId);
    }

    /**
     * 用戶列表 > 用戶修改
     */
    public function userModify()
    {
        $model = $this->model;
        $this->menu->currentName = "用戶修改";
        if ($model->submit)//更新
        {
            $result = $model->updateUserDetail();
            if ($result) {
                $this->redirect("userInfo/userList");
            }
        }
        $this->getDetailSelect();
        $model->empSelect2["selected"] = $model->user["ParentID"];
        return parent::render("userDetail");
    }

    public function routeSearch()
    {
        $model = $this->model;
        if ($model->submit)//更新
        {
            $model->getRouteSearch();
        }
        $model->empSelect2 = EmpHelper::getEmpSelect($model->empSelect,
            ["selected" => $model->userId]);
        return parent::render();
    }

    public function getBalance()
    {

    }

    public function ajax_setGroupName()
    {
        $this->model->setGroupName();
    }
}

?>
