<?php
//$menuj = "{
//    'env_setting':{
//        'network_setting':{
//
//        }
//    }
//}";
/*
session_start();
class Menu
{
    public static $currectName = "";

    private $HighestManager = array(
//        "group_call_setting" => array(
//            "name" => "群呼設定",
            "user_info" => array(
                "name" => "用戶資訊",
                "user_list" => array(
                    "name" => "用戶列表",
                    "url" => "userInfo/userList"
                ),
                "add_retes" => array(
                    "name" => "新增費率",
                    "url" => "userInfo/addRates"
                ),
                "all_rates" => array(
                    "name" => "所有費率",
                    "url" => "userInfo/allRates"
                ),
                "user_route" => array(
                    "name" => "用戶路由",
                    "url" => "userInfo/userRoute"
                ),
                "route_search" => array(
                    "name" => "路由查詢",
                    "url" => "userInfo/routeSearch"
                ),
            ),
            "extension_info" => array(
                "name" => "分機資訊",
                "add_extension" => array(
                    "name" => "新增分機",
                    "url" => "extensionInfo/addExtension"
                ),
                "extension_manage" => array(
                    "name" => "分機管理",
                    "url" => "extensionInfo/extensionManage"
                ),
                "seat_tactics" => array(
                    "name" => "座席策略",
                    "url" => "extensionInfo/seatTactics"
                )
            ),
            "communication_history" => array(
                "name" => "通聯紀錄",
                "communication_search" => array(
                    "name" => "通聯查詢",
                    "url" => "communicationHistory/communicationSearch"
                ),
                "task_ranking" => array(
                    "name" => "話務排行",
                    "url" => "communicationHistory/taskRanking"
                ),
                "point_history" => array(
                    "name" => "儲值紀錄",
                    "url" => "communicationHistory/pointHistory"
                )
            )
//        )
    );
    private $NormalManager = array(
        "group_call_setting" => array(
            "name" => "群呼設定",
            "add_group_call" => array(
                "name" => "新增群呼",
                "url" => "groupCallSetting/addGroupCall"
            ),
            "group_call_schedule" => array(
                "name" => "群呼流程",
                "url" => "groupCallSetting/groupCallSchedule"
            ),
            "seat_tactics" => array(
                "name" => "座席策略",
                "url" => "extensionInfo/seatTactics"
            ),
            "extension_manage" => array(
                "name" => "分機管理",
                "url" => "extensionInfo/extensionManage"
            )
        ),
        "communication_history" => array(
            "name" => "通聯紀錄",
            "communication_search" => array(
                "name" => "通聯查詢",
                "url" => "communicationHistory/communicationSearch"
            ),
            "task_ranking" => array(
                "name" => "話務排行",
                "url" => "communicationHistory/taskRanking"
            ),
            "point_history" => array(
                "name" => "儲值紀錄",
                "url" => "communicationHistory/pointHistory"
            ),
            "record_download" => array(
                "name" => "錄音下載",
                "url" => "communicationHistory/recordDownload"
            )
        ),
        "sys_lookout" => array(
            "name" => "系統監視",
            "call_status" => array(
                "name" => "呼叫狀態",
                "url" => "sysLookout/callStatus"
            ),
            "key_methor" => array(
                "name" => "按鍵功能",
                "url" => "sysLookout/keyＭethor"
            )
        )
    );
    private $NormalUser = array();
    private $LowLevelUser = array();
    private $NoPermission = array();

    private $menus = array();
    private $menu_html = "";

    public function __construct($base){
        $this->base = $base;
        $this->menus[] = $this->HighestManager;
        $this->menus[] = $this->NormalManager;
        $this->menus[] = $this->NormalUser;
        $this->menus[] = $this->LowLevelUser;
        $this->menus[] = $this->NoPermission;
    }

    public function CreateMenu($permission)
    {

//        $this->menu = $_SESSION["menu"] ? $_SESSION["menu"]: $this->ReadSubMenu($this->menus,$permission);
//        $_SESSION["menu"] = $this->menu;
        $this->menu_html = $this->ReadSubMenu($this->menus[$permission]);
        return $this->menu_html;

    }

    private function ReadSubMenu($subMenu,$floor=0)
    {
        if(!is_array($subMenu))
        {
            return ;
        }

        $last_li = "";
        switch($floor)
        {
            case 0:
                $class = "mainmenu sidebar-nav";
                $space_li = "<li style='list-style-type: none;visibility: hidden;height:20px;'>a</li>";
                break;
            case 1:
                $class = "submenu";
                break;
            case 2:
                $class = "lastmenu";
                break;
        }
        $body = "";
        foreach($subMenu as $key=>$val){
            if($key=="name")
                continue;

            if(isset($val["url"]))
            {
                $tmp_class = "";
                if(strpos($_SERVER['REQUEST_URI'],$this->base["folder"].$val["url"])!==false)
                {
                    self::$currectName = $val["name"];
                    $tmp_class = "slideactive";
                }
                $body .= "<li>";
                $class = "lastmenu";
                $body .= "<a class='$tmp_class' href='".$this->base["folder"].$val["url"]."'>".$val["name"]."</a>";
                $body .= "</li>";
            }
            else
            {
                $body .= "<li>";
                $body .= "<a>".$val["name"]."</a>".$this->ReadSubMenu($val,$floor+1);
                $body .= "</li>";
            }

        }

        return "<ul class = '$class'>{$space_li}{$body}{$space_li}</ul>";
    }
}*/


?>