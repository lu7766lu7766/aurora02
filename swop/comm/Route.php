<?php

/**
 *
 */

namespace comm;

/**
 * Class Route
 * @package comm
 */

class Route
{
    static private $router;

    /** 加入get route表 */
    static public function get(...$args)
    {
        return self::addMethod(Method::GET, ...$args);
    }

    /** 加入post route表 */
    static public function post(...$args)
    {
        return self::addMethod(Method::POST, ...$args);
    }

    /** 加入put route表 */
    static public function put(...$args)
    {
        return self::addMethod(Method::PUT, ...$args);
    }

    /** 加入delete route表 */
    static public function delete(...$args)
    {
        return self::addMethod(Method::DELETE, ...$args);
    }

    /** 加入any route表 */
    static public function any(...$args)
    {
        return self::addMethod(Method::ANY, ...$args);
    }

    /** 加入route表 */
    static private function addMethod($method, ...$args)
    {
        if (!self::$router)
        {
            self::$router = new Router();
        }
        self::$router->setPath($args[0]);
        self::$router->map[$args[0]] = [
            'method' => $method,
            'callback' => $args[1]
        ];

        return self::$router;
    }

}

class Router
{
    private $path = "";
    public $map = [];
    public $config = [];

    /**
     * regular
     * @param ...$args key, regular /OR/ [ key1 => reg1, key2 => reg2...]
     * @return $this
     */
    public function where(...$args)
    {
        $where = [];
        if (is_array($args[0]))
        {
            $where = $args[0];
        } else if (count($args) == 2) {
            $where = [$args[0] => $args[1]];
        }
        $this->map[$this->path]["where"] = $where;
        return $this;
    }

    /** 設定名稱，未有實際功能 */
    public function name($name)
    {
        $this->map[$this->path]["name"] = $name;
        return $this;
    }

    /** 尚未有功能 */
    public function middleware()
    {
        return $this;
    }

    /** 設定環境變數 */
    public function __construct()
    {
        global $config;
        $this->config = $config;
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $air = explode('?', str_replace($this->config->base["folder"], "/", $_SERVER['REQUEST_URI']))[0];
        $isMatched = false;
        /** 開始mapping */
        foreach ($this->map as $path => $args)
        {
            /** 路徑長度檢測 */
            if (count(explode('/', $air)) != count(explode('/', $path))) continue ;

            $pregvar = [];
            $req = [];
            $len = 0;
            $method = $args['method'];
            $func = $this->procFunc($args['callback']);
            $wheres = $args['where'];

            /** method檢測，any沒有檢查必要 */
            if (
                $method !== Method::ANY &&
                !(
                    $_SERVER['REQUEST_METHOD'] === Method::POST && $_POST["_method"] === strtoupper($method) || // delete or put
                    $_SERVER['REQUEST_METHOD'] === $method
                ) // get or post
            ){
                continue ;
            }

            /** 找到所有的變數名稱 */
            $tmpPath = $path;
            preg_match_all('/\/{([a-zA-Z0-9]+)}/', $tmpPath, $match);

            if ($match)
            {
                $len = count($match[1]);
                for ($i = 0; $i<$len; $i++)
                {
                    $pregvar[$match[1][$i]] = '[^\/]+';
                }
            }

            $wheres = array_merge($pregvar, is_array($wheres)? $wheres: []);
            /** 先將路徑全部加上跳脫字元，含頭尾 */
            $path = "/".strtr($path, ["/"=>"\\/", ])."/";
            foreach ($wheres as $var => $where)
            {
                $path = strtr($path, ['{'. $var. '}' => '('.$where.')']);
                $req[$var] = '';
            }
            /** 取出所有的變數 */
            preg_match($path, $air, $match);

            if ($len == count($match)-1)
            {
                array_shift($match);
                foreach ($req as $var => $val)
                {
                    $val = array_shift($match);
                    $req[$var] = $val;
                }
                /** 取method變數 */
                switch ($method) {
                    case Method::GET:
                        $data["get"] = $this->collectMethod(Method::GET);
                        break;
                    case Method::POST:
                    case Method::PUT:
                    case Method::DELETE:
                    $data["post"] = $this->collectMethod(Method::POST);
                        break;
                    case Method::ANY:
                        $data["get"] = $this->collectMethod(Method::GET);
                        $data["post"] = $this->collectMethod(Method::POST);
                        break;
                }
                /** 塞入共用變數 */
                $controller = $this->config->base["default_controller"];
                $action = $this->config->base["default_action"];
                if (is_string($args['callback']) && strpos($args['callback'], "@")!==false) {
                    list($controller, $action) = explode("@", $args['callback']);
                }
                $data['submit_link'] = $this->config->base['folder'].$controller."/".$action;
                $data["controller"] = $controller;
                $data["action"] = $action;
                $data["top_layout"] = "shared/top.php";
                $data["layout"] = $action;
                $data["bottom_layout"] = "shared/bottom.php";

                /** @var callback $func */
                $func($req);
                $isMatched = true;
                break;
            }
        }

        /** 沒有符合route表則走舊式 */
        if (!$isMatched)
        {
//            $base_hierarchy = explode("/", $air);
//
//            $base_url = $this->config->base['controller_dir'] . $base_hierarchy[0] . ".php";
//
//            $store_pos = 2;
//            if (file_exists($base_url)) // 驗證 controller 是否存在
//            {
//                $controller = $base_hierarchy[0];
//            }
//            else
//            {
//                $controller = $this->config->base["default_controller"];
//                $base_url = $this->config->base['controller_dir'] . $this->config->base["default_controller"] . ".php";
//                $store_pos--;
//            }
//            $controller_class = $controller."_Controller";
//
//            require_once $base_url;
//            $swop = new $controller_class($this->config->base);//Env_Controller($base);
//
//            if (isset($base_hierarchy[1]) && method_exists($swop, $base_hierarchy[1])) //驗證 controller 與 action 是否存在
//            {
//                $action = $base_hierarchy[1];
//            }
//            else
//            {
////    if(strpos($air,"downloadAdCommunicationSearch")!==false)
////    {
////        die(); // 一個神奇的bug，沒有這行就會進來，導致excel無法下載
////    }
//                $swop->redirect("index/index");
//                $action = $this->config->base["default_action"];
//                $store_pos--;
//            }
//
//            $data['submit_link'] = $this->config->base['folder'].$controller."/".$action;
//            $data["controller"] = $controller;
//            $data["action"] = $action;
//            $data["top_layout"] = "shared/top.php";
//            $data["layout"] = $action;
//            $data["bottom_layout"] = "shared/bottom.php";
//
//
//            $a_get = array();
//            $len = count($base_hierarchy);
//            for ($i = $store_pos; $i < $len; $i += 2)
//            {
//                if (isset($base_hierarchy[$i + 1]) && $base_hierarchy[$i])
//                {
//                    $a_get[$base_hierarchy[$i]] = $base_hierarchy[$i + 1];
//                }
//            }
//            foreach ($_GET as $key => $val)
//            {
//                $a_get[$key] = $val;
//            }
//            $data["get"] = $a_get;
//
//            $a_post = array();
//            foreach ($_POST as $key => $val)
//            {
//                $a_post[$key] = $val;
//            }
//
//            $data["post"] = $a_post;
//
//            $swop->getData($data);
//
//            $swop->$action();
        }
    }

    /**
     * proccess function
     * @param string /OR/ function
     * @return function
     */
    private function procFunc ($arg)
    {
        if (is_callable($arg))
        {
            return $arg;
        } else if (is_string($arg)) {
            $map = explode('@', $arg);
            //dirname(dirname(__DIR__))."/". $this->config->base['controller_dir'] . "adCallSetting.php" ;

            $file_path = dirname(__DIR__). "/". $this->config->base['controller']. $map[0]. ".php";

            if (file_exists($file_path))
            {
                require_once($file_path);
                $swop = new $map[0]($this->config->base);
                if (count($map) == 2 && method_exists($swop, $map[1]))
                {
                    return function ($req) use ($swop, $map){
                        $swop->{$map[1]}($req);
                    };
                } else {
                    return function ($req) use ($swop){
                        return $swop->{$this->config->base['default_action']}($req);
                    };
                }
            }
            throw new Exception('don\'t find the controller!!');
        }
    }

    /**
     * get tmp path
     * @param string // route url
     */
    public function setPath ($path)
    {
        $this->path = $path;
    }

    /**
     * 收集GET or POST 所有變數
     * @param string // Method::GET or Method::POST
     */
    private function collectMethod (string $method = Method::GET) {
        $collectrion = [];
        switch($method){
            case Method::GET:
                $collectrion = $_GET;
            break;
            case Method::POST:
                unset($_POST["_method"]);
                $collectrion = $_POST;
                break;
        }
        $res = [];
        foreach ($collectrion as $key => $val)
        {
            $res[$key] = $val;
        }
        return $res;
    }
}

class Method
{
    const  ANY = 'ANY';
    const  GET = 'GET';
    const  POST = 'POST';
    const  PUT = 'PUT';
    const  DELETE = 'DELETE';
}
?>