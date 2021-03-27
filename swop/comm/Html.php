<?php

class Html
{
    static public $Post = "post";
    static public $Get = "GET";

    static public function form($method = "post", $action = "")
    {
        $html = "<form action='$action' method='$method' enctype='multipart/form-data' autocomplete='off'>";
        $html .= "<input type='hidden' name='submit' value='1'/>";
        $html .= "<input type='hidden' id='status' name='status'/>";
        return $html;
    }

    static public function formEnd()
    {
        return "</form>";
    }

    static public function selector($a, $select = "")
    {
        $a["id"] = $a["id"] ?? $a["name"];
        $a["name"] = $a["name"] ?? $a["id"];
        $a["class"] = $a["class"] ?? "form-control";
        if (!empty($select)) {
            $a["selected"] = $select;
        }
        $html = "<select id='" . $a["id"] . "' name='" . $a["name"] . "' class='" . $a["class"] . "'>";
        if (is_array($a["option"])) {
            foreach ($a["option"] as $option_val) {
                $attr = "";
                if ($option_val["attr"]) {
                    foreach ($option_val["attr"] as $key => $attr_val) {
                        $attr .= (empty($attr_val)
                            ? " {$key} "
                            : " {$key}={$attr_val} ");
                    }
                }
                $attr .= ($option_val["value"] == $a["selected"]) ? " selected " : "";
                $html .= "<option value='" . $option_val["value"] . "' {$attr}>" . $option_val["name"] . "</option>";
            }
        }
        $html .= "</select>";
        return $html;
    }

    static public function pageInput($page = 0, $last_page, $per_page = 50)
    {
        //三個參數都要在model就取得
        $html = "<input type='hidden' id='page' name='page' value='$page'/>";
        $html .= "<input type='hidden' id='last_page' name='last_page' value='$last_page'/>";
        $html .= "<input type='hidden' id='per_page' name='per_page' value='$per_page'/>";
        return $html;
    }
}

?>
