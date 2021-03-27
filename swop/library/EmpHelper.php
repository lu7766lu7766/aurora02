<?php

class EmpHelper
{
    static public function getEmpSelect2($empSelect, $attr)
    {
        $select2 = self::getEmpSelect($empSelect, $attr);
        foreach ($select2["option"] as $i => $option) {
            if ($option["value"] != "" &&
                $option["value"] != $attr["choice"] &&
                !in_array($option["value"], $attr["current_sub_emp"])) {
                unset($select2["option"][$i]);
            }
        }
        return $select2;
    }

    static public function getEmpSelect($empSelect, $attr)
    {
        $empSelect2 = $empSelect;
        foreach ($empSelect2["option"] as $key => $item) {
            unset($empSelect2["option"][$key]["attr"]["permission"]);
        }
        $empSelect2["name"] = $attr["name"] ?? "userId";
        $empSelect2["id"] = $attr["id"] ?? $attr["name"] ?? "userId";
        $empSelect2["selected"] = $attr["selected"] ?? "";
        $empSelect2["class"] = $attr["class"] ?? "form-control";
        if (is_array($attr["attr"])) {
            foreach ($attr["attr"] as $key => $val) {
                $empSelect2[$key] = $val;
            }
        }
        if (is_array($attr["option"])) {
            array_unshift($empSelect2["option"], $attr["option"]);
        }
        return $empSelect2;
    }

    static public function getCurrentSubEmp($a_sub_emp, $user_id)
    {
        $result = [$user_id];
        foreach ($a_sub_emp as $sub_emp) {
            if ($sub_emp["ParentID"] == $user_id) {
                $result = array_merge($result, EmpHelper::getCurrentSubEmp($a_sub_emp, $sub_emp["UserID"]));
            }
        }
        return $result;
    }

    static public function getMenuList($emps, $user_id)
    {
        foreach ($emps as $emp) {
            if ($emp["UserID"] == $user_id) {
                return $emp["MenuList"];
            }
        }
    }

    static public function getPermissionControl($emps, $user_id)
    {
        foreach ($emps as $emp) {
            if ($emp["UserID"] == $user_id) {
                return $emp["PermissionControl"];
            }
        }
    }

    static public function KillValue($empSelect, $value = "")
    {
        if (empty($value)) {
            return $empSelect;
        }
        $targetIndex = 0;
        foreach ($empSelect["option"] as $key => $opval) {
            if ($opval["value"] == $value) {
                $targetIndex = $key;
                break;
            }
        }
        array_splice($empSelect["option"], $targetIndex, 1);
        return $empSelect;
    }
}
