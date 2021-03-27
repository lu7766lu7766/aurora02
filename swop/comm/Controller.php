<?php

class Controller
{
    public $checkLogin = true;

    public function __construct($base)
    {
        $this->base = $base;
        $this->defaultUrl = $base["default_controller"] . "/" . $base["default_action"];
    }

    public function getData($data)
    {
        foreach ($data as $key => $val) {
            $this->$key = $val;
        }
        $controller = $this->controller;
        $base = $this->base;
        $model_url = $this->base["model_dir"] . $controller . ".php";
        if (file_exists($model_url)) {
            require_once $model_url;
            $model_class = $controller . "_Model";
            $this->model = new $model_class($base);
        } else {
            $this->model = new JModel($base);
        }
        if (is_array($this->get)) {
            foreach ($this->get as $key => $val) {
                if (isset($key) && isset($val)) {
                    $this->model->$key = urldecode(preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $val) ? strtr($val,
                        ["-" => "/"]) : $val);
                }
            }
            $this->get = null;
        }
        if (is_array($this->post)) {
            foreach ($this->post as $key => $val) {
                if (isset($key) && isset($val)) {
                    $this->model->$key = $val;
                }
            }
            $this->post = null;
        }
//            $this->redirect($base["default_controller"]."/".$base["default_action"]);
        if ($controller != $base["default_controller"]) {
            if ($this->checkLogin && !$this->model->session["login"]) {
                $this->redirect($this->defaultUrl);
            }
        }
        if (is_array($this->model->session["model"])) {
            foreach ($this->model->session["model"] as $key => $val) {
                if (isset($key) && isset($val)) {
                    $this->model->$key = $val;
                }
            }
            $this->model->session["model"] = null;
        }
        $action = $this->action;
        if (method_exists($this->model, $action)) {
            $this->model->$action();
        }
    }

    public function redirect($url = "", $model = [])
    {
        $this->model->session["model"] = $model;
        if (!$url) {
            $url = $this->defaultUrl;
        }
        $url = trim($url, "/");
        $url = $this->base["url"] . $url;
        header("location:" . $url);
    }

    public function partialView($url)
    {
        $base = $this->base;
        $model = $this->model;
        $url = str_replace("~/", $base["folder"], $url);
        if (file_exists($url)) {
            include_once $url;
        }
    }

    public function getModel($controller)
    {
        include_once $this->base["model_dir"] . $controller . ".php";
        $model_name = $controller . "_Model";
        return new $model_name($this->base);
    }

    /**
     * @return 根目錄
     */
    public function getRootPath()
    {
        return dirname(dirname(__DIR__)) . '\\';
    }

    protected function render($view = null)
    {
        $base = $this->base;
        $model = $this->model;
        if (!$view || !is_string($view)) {
            $view = $this->layout;
        }
        $top_view_path = $base['view_dir'] . $this->top_layout;
        $view_path = $base['view_dir'] . $this->controller . "/" . $view . ".php";
        $bottom_view_path = $base['view_dir'] . $this->bottom_layout;
        if (file_exists($view_path)) {
            include_once $view_path;
        } else {
            $this->redirect("index/index");
        }
        return $this;
    }
}

?>
