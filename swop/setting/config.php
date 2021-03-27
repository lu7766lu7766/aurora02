<?php
namespace setting;

class Config
{
    public $base = [];

    public function __construct()
    {
        $base["version"] = "2018110201";
        $base["default_controller"] = "main";
        $base["default_action"] = "main";
        $base["file"] = "file://";
        $base["http"] = "http://";
        $base["https"] = "https://";
        $base["folder"] = "/aurora02/"; //dirname(dirname(__DIR__)). "/"; // "/aurora02/"
        $base["root_folder"] = dirname(dirname(__DIR__)) . "/";
        $base['url'] = $base["http"] . $_SERVER['HTTP_HOST'] . $base["folder"]; // $base['folder'];
        $base['swop'] = $base["root_folder"] . "swop/";
        $base['swop_uri'] = "swop/";
        $base['model_dir'] = $base['swop'] . "model/";
        $base['controller'] = "controller/";
        $base['controller_dir'] = $base['swop'] . $base['controller'];
        $base['view_dir'] = $base['swop'] . "view/";
        $base['setting_uri'] = $base['swop_uri'] . "setting/";
        $base['setting_dir'] = $base['swop'] . "setting/";
        $base['comm_dir'] = $base['swop'] . "comm/";
        $base['library_dir'] = $base['swop'] . "library/";
        $base['language_dir'] = $base['swop'] . "language/";
        $base['lang'] = "tw";
        $base['public'] = $base["root_folder"] . "public/";
        $base['js_dir'] = $base['public'] . "js/";
        $base['css_dir'] = $base['public'] . "css/";
        $base['img_dir'] = $base['public'] . "img/";
        $base['tpl'] = $base['public'] . "img/template/";
        $base['download'] = $base["root_folder"] . "download/";
        $base['record'] = $base["download"] . "record/";
        $base['communicationSearch'] = $base['download'] . "communicationSearch/";
        $base['callStatus'] = $base['download'] . "callStatus/";
        $base['sweep'] = $base['download'] . "sweep/";
        $base['voiceManage'] = $base['download'] . "voiceManage/";
        $base['cn_phone_rule'] = $base['setting_dir'] . "cn_phone_rule.json";
        $base['tw_phone_rule'] = $base['setting_dir'] . "tw_phone_rule.json";
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $base['ip'] = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $base['ip'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $base['ip'] = $_SERVER["REMOTE_ADDR"];
        }
        $this->base = $base;
    }
}

//function start_session($expire = 1*60*90)
//{
//    //default 90 mins
//
//    if ($expire == 0) {
//        $expire = ini_get('session.gc_maxlifetime');
//    } else {
//        ini_set('session.gc_maxlifetime', $expire);
//    }
//
//    if (empty($_COOKIE['PHPSESSID'])) {
//        session_set_cookie_params($expire);
//        session_start();
//    } else {
//        session_start();
//        setcookie('PHPSESSID', session_id(), time() + $expire);
//    }
//}
?>
