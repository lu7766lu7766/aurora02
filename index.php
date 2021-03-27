<?php
//session_start();
//start_session();
//ini_set('session.cookie_lifetime', 1);
//ini_set('session.gc_maxlifetime', 1);
date_default_timezone_set("Asia/Taipei");
set_time_limit(0);
session_start();
//echo ini_get("session.gc_maxlifetime");
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);
require('vendor/autoload.php');
require('swop/library/helper.php');

// require_once "swop/setting/config.php";
use setting\Config;

$config = new Config();

// require_once Config::$base['setting_dir']."menu2.php";
use voku\helper\AntiXSS;

$antiXss = new AntiXSS();
if (is_array($_GET)) {
    foreach ($_GET as $key => $value) {
        if ($value != '') {
            $_GET[$key] = $antiXss->xss_clean($value);
        }
    }
}
if (is_array($_POST)) {
    foreach ($_POST as $key => $value) {
        if ($value != '') {
            $_POST[$key] = $antiXss->xss_clean($value);
        }
    }
}

use comm\Route;
// .env start
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');
//echo getenv('DB_USER');
// .env end
// crypt start
//echo \lib\Hash::encode('lu7766');
//echo \lib\Hash::decode(\lib\Hash::encode('lu7766'));
// crypt end
Route::get("/main/main/{id}", "APIController@test")
    ->where(["id" => "\\d+"]);
Route::get("/main/{guid}", function ($id) {
    echo "main/main";
})->where("guid", "[0-9]+");
// composer autoload
// require_once $base['language_dir'] . $base['lang'] . "/common.php";
//
// require_once $base['comm_dir']."Controller.php";
// require_once $base['comm_dir']."Crypt.php";
// require_once $base['comm_dir']."Html.php";
// require_once $base['comm_dir']."Http.php";
// require_once $base['comm_dir']."Model.php";
//
//
// require_once $base['library_dir']."JController.php";
// require_once $base['library_dir']."JModel.php";
// require_once $base['library_dir']."Bundle.php";
// require_once $base['library_dir']."PageHelper.php";
// require_once $base['library_dir']."EmpHelper.php";
$air = explode('?', str_replace($config->base["folder"], "", $_SERVER['REQUEST_URI']))[0];
//if (substr_count($air, "?") > 0)
//{
//    $url = $base["folder"];
//    $get_parameter = explode("?", $air);
//    $url .= $get_parameter[0];
//    $get_parameter = explode("&", $get_parameter[1]);
//    foreach ($get_parameter as $value)
//    {
//        $parameter = explode("=", $value);
//        $url .= "/" . $parameter[0] . "/" . strtr($parameter[1],["/"=>"-"]);
//    }
//    $_SESSION[$base["folder"]]["model"] = $_POST;
//    header("location:" . $url);
//}
$base_hierarchy = explode("/", $air);
$base_url = $config->base['controller_dir'] . $base_hierarchy[0] . ".php";
$store_pos = 2;
if (file_exists($base_url)) // 驗證 controller 是否存在
{
    $controller = $base_hierarchy[0];
} else {
    $controller = $config->base["default_controller"];
    $base_url = $config->base['controller_dir'] . $config->base["default_controller"] . ".php";
    $store_pos--;
}
$controller_class = $controller . "_Controller";
require_once $base_url;
$swop = new $controller_class($config->base);//Env_Controller($base);
if (isset($base_hierarchy[1]) && method_exists($swop, $base_hierarchy[1])) //驗證 controller 與 action 是否存在
{
    $action = $base_hierarchy[1];
} else {
//    if(strpos($air,"downloadAdCommunicationSearch")!==false)
//    {
//        die(); // 一個神奇的bug，沒有這行就會進來，導致excel無法下載
//    }
//    die($controller_class . "^^" . $base_hierarchy[0] . "^^" . $base_hierarchy[1]);
    $swop->redirect("index/index");
    $action = $config->base["default_action"];
    $store_pos--;
}
$data['submit_link'] = $config->base['folder'] . $controller . "/" . $action;
$data["controller"] = $controller;
$data["action"] = $action;
$data["top_layout"] = "shared/top.php";
$data["layout"] = $action;
$data["bottom_layout"] = "shared/bottom.php";
$a_get = [];
$len = count($base_hierarchy);
for ($i = $store_pos; $i < $len; $i += 2) {
    if (isset($base_hierarchy[$i + 1]) && $base_hierarchy[$i]) {
        $a_get[$base_hierarchy[$i]] = $base_hierarchy[$i + 1];
    }
}
foreach ($_GET as $key => $val) {
    $a_get[$key] = $val;
}
$data["get"] = $a_get;
$a_post = [];
foreach ($_POST as $key => $val) {
    $a_post[$key] = $val;
}
$data["post"] = $a_post;
$swop->getData($data);
$swop->$action();
//function clean ($data)
//{
//	if (!is_string($data)) {
//		return $data;
//	}
//	if (!get_magic_quotes_gpc()) {
//		$data = addslashes($data);
//	}
//	//$data = str_replace("_", "\_", $data);
//	$data = str_replace("%", "\%", $data);
//	$data = nl2br((string)$data);
//	$data = htmlspecialchars($data);
//
//	return $data;
//}
function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
        mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

?>
