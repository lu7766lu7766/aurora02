<?php

class JController extends Controller
{
    public function __construct($base)
    {
        parent::__construct($base);
        $this->menu = new setting\Menu2($base);
    }

    public function getData($data)
    {
        parent::getData($data);
        $this->menu->setModel($this->model);
        $this->selector();
    }

    public function selector()
    {
        $model = $this->model;
        $model->empSelect = [
            "id"       => "userSelet",
            "name"     => "userSelet",
            "class"    => "",
            "option"   => [],
            "selected" => $model->session["choice"]
        ];
        //print_r($model->subEmp);
        $model->empSelect["option"][] = [
            "value" => $model->user["UserID"],
            "name"  => $model->user["UserID"] . " (" . $model->user["UserName"] . ")",
            "attr"  => [
                "permission" => $model->user["MenuList"]//$model->user["UserGroup"]//$model->user["MenuList"]
            ]
        ];
        if (is_array($model->subEmp)) {
            foreach ($model->subEmp as $emp) {
                $a_emp = [];
                $a_emp["value"] = $emp["UserID"];
                $a_emp["name"] = $emp["UserID"] . " (" . $emp["UserName"] . ")";
                $a_emp["attr"] = [
                    "permission" => $emp["MenuList"]//$emp["UserGroup"]//$emp["MenuList"]
                ];
                $model->empSelect["option"][] = $a_emp;
            }
        }
        //print_r($model->empSelect["option"]);
    }

    public function render($view = null)
    {
        Bundle::addLink("jquery");
        Bundle::addLink("bootstrap");
        Bundle::addLink("default");
        parent::render($view);
    }

    public function getTime()
    {
        echo date("Y-m-d H:i:s", time());
    }

    public function getConcurrentCallsSelect()
    {
        $this->model->concurrentCallsSelect = [
            "id"       => "concurrentCalls",
            "name"     => "concurrentCalls",
            "class"    => "form-control",
            "option"   => [],
            "selected" => $this->model->data["ConcurrentCalls"]
        ];
        $times = 30;
        while ($times) {
            $this->model->concurrentCallsSelect["option"][] = [
                "value" => $times,
                "name"  => "每 1 秒 " . $times-- . " 通"
            ];
        }
    }

    public function arrayDownload($array, $fileName)
    {
        $txt = join("\r\n", $array);
        $filePath = "download/" . $fileName;
        @mkdir($this->base["download"]);
        file_put_contents($fileName, $txt);
        rename($fileName, $filePath);
        $fileSize = filesize($filePath);
        $this->downloadFile($filePath, $fileName);
        @unlink($filePath);
    }

    public function downloadFile($path, $fileName = "")
    {
        $fileSize = filesize($path);
        $list = explode("/", $path);
        if (empty($fileName)) {
            $fileName = count($list) > 1 ? end($list) : end(explode("\\", $path));
        }
        header('Pragma: public');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename="' . $fileName . '";');
        header('Content-Transfer-Encoding: binary');
        readfile($path);
    }

    public function exportExcel($fileName)
    {
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename={$fileName}");
    }
}

?>
