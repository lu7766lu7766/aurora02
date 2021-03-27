<?php

class SysSweep_Controller extends JController
{
    public function sweepStatus()
    {
        parent::render();
    }

    public function ajaxSweepStatusContent()
    {
        $this->model->getAjaxSweepStatusContent();
        $this->getConcurrentCallsSelect();
        $this->model->concurrentCallsSelect["class"].= " ajaxChgConcurrentCalls";
        parent::render();
    }

    public function ajaxCallStatusContent2()
    {
        $this->model->getAjaxSweepStatusContent();
        $tmp_model = new stdClass();
        $tmp_model->data3 = $this->model->data3;
        $tmp_model->waitExtensionNoCount = $this->model->waitExtensionNoCount??"";
        $tmp_model->extensionNoCount = $this->model->extensionNoCount??"";
        $tmp_model->balance = $this->model->balance;
        echo json_encode($tmp_model);
    }

    public function ajaxChgConcurrentCalls()
    {
        $result = $this->model->chgConcurrentCalls();
        echo $result?"success":$result;
    }

    public function ajaxCallRelease()
    {
        $url = "http://127.0.0.1:60/CallRelease.atp?Seat=".$this->model->Seat."&CalledID=".$this->model->CalledID;
        comm\Http::get($url);
    }

    public function ajaxDeleteSearchPlan()
    {
        $result = $this->model->deleteSearchPlan();
        echo $result?"success":$result;
    }

    public function ajaxRecall()
    {
        $result = $this->model->updateRecall();
        echo $result?"success":$result;
    }

    public function ajaxSearchSuspendSwitch()
    {
        return $this->model->updateSearchSuspendSwitch();
    }

    public function ajaxMaxRoutingCalls()
    {
        $result = $this->model->updateMaxRoutingCalls();
        echo $result?"success":$result;
    }

    public function ajaxMaxSearchCalls()
    {
        $result = $this->model->updateMaxSearchCalls();
        echo $result?"success":$result;
    }

    public function ajaxSearchAutoStartTime()
    {
        $result = $this->model->updateSearchAutoStartTime();
        echo $result?"success":$result;
    }

    public function ajaxcSearchAutoStopTime()
    {
        $result = $this->model->updateSearchAutoStopTime();
        echo $result?"success":$result;
    }

    public function ajaxUseState()
    {
        return $this->model->updateUseState();
    }

    public function downloadCallUnavailable()//無效
    {
        $this->model->getDownloadCallUnavailable();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadCallAvailable()//有效
    {
        $this->model->getDownloadCallAvailable();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadCallConCount()//接通
    {
        $this->model->getDownloadCallConCount();
        $this->fileDownload($this->model->filePath);
    }

    private function fileDownload($filePath)
    {
        $fileName = end(explode("/",$filePath));
        $fileSize = filesize($filePath);
        header('Pragma: public');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename="' . $fileName . '";');
        header('Content-Transfer-Encoding: binary');
        readfile($filePath);
    }
}

?>