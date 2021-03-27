<?php

class Main_Controller extends Controller
{
    public function main()
    {
        $model = $this->model;
        if ($model->submit) {
//            if(true)
            if (strtolower($model->captcha) == strtolower($_SESSION["code_login"])) {
                if ($model->login_in()) {
                    $this->redirect("index/index");
                } else {
                    $model->errorMsg = "帳號或密碼錯誤";
                }
            } else {
                if (!$_SESSION["code_login"] || !$model->captcha) {
                    $model->errorMsg = "伺服器錯誤";
                    $model->dba->server_restart();
                    header("Refresh:0");
                } else {
                    $model->errorMsg = "驗證碼錯誤";
                }
            }
        }
        Bundle::addLink("jquery");
        Bundle::addLink("bootstrap");
        Bundle::addLink("default");
        return $this->render();
    }

    public function code_login()
    {
        require_once $this->base["comm_dir"] . "verification.php";;
        $vert = new Library_Verification();
        $vert->kernel();
        $_SESSION["code_login"] = $vert->code;
        echo $_SESSION["code_login"] . "^^";
    }

    public function server_restart()
    {
        $this->model->dba->server_restart();
        $this->redirect($this->defaultUrl);
//        echo $this->defaultUrl . "^^";
    }
}

?>
