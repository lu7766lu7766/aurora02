<?php

class SysLookout_Controller extends JController
{
    public function callStatus()
    {
        $this->model->calloutGroupIDSelect = [
            "id"     => "calloutGroupID",
            "name"   => "calloutGroupID",
            "class"  => "form-control ajaxChgCalloutGroupID",
            "option" => []
        ];
        parent::render();
    }

    public function callStatus_vue()
    {
        $this->model->callStatus();
        $this->getConcurrentCallsSelect();
        $this->getCalloutGroupIDSelect();
        parent::render();
    }

    private function getCalloutGroupIDSelect()
    {
        $this->model->calloutGroupIDSelect = [
            "id"     => "calloutGroupID",
            "name"   => "calloutGroupID",
            "class"  => "form-control ajaxChgCalloutGroupID",
            "option" => []
        ];
        $times = 1;
        while ($times <= 4) {
            $this->model->calloutGroupIDSelect["option"][] = [
                "value" => $times,
                "name"  => $times++
            ];
        }
    }

    public function keyMethod()
    {
        parent::render();
    }

    public function ping()
    {
        parent::render();
    }

    public function ajaxPing()
    {
        $this->model->getPing();
    }

    public function ajaxCallStatusContent()
    {
        $this->model->getAjaxCallStatusContent();
        $this->getConcurrentCallsSelect();
        $this->model->concurrentCallsSelect["class"] .= " ajaxChgConcurrentCalls";
        $this->getCalloutGroupIDSelect();
        parent::render();
    }

    public function ajaxCallStatusContent2()
    {
        $this->model->getAjaxCallStatusContent();
        $tmp_model = new stdClass();
        $tmp_model->data1 = $this->model->data1;
        $tmp_model->data2 = $this->model->data2;
        $tmp_model->data3 = $this->model->data3;
        $tmp_model->waitExtensionNoCount = $this->model->waitExtensionNoCount ?? "";
        $tmp_model->extensionNoCount = $this->model->extensionNoCount ?? "";
        $tmp_model->balance = $this->model->balance;
        $tmp_model->suspend = $this->model->suspend;
        echo json_encode($tmp_model);
    }

    public function ajaxChgConcurrentCalls()
    {
        $result = $this->model->chgConcurrentCalls();
        echo $result ? "success" : $result;
    }

    public function ajaxChgCalloutGroupID()
    {
        $result = $this->model->chgCalloutGroupID();
        echo $result ? "success" : $result;
    }

    public function ajaxCallRelease()
    {
        $url = "http://127.0.0.1:60/CallRelease.atp?Seat=" . $this->model->Seat . "&CalledID=" . $this->model->CalledID;
        comm\Http::get($url);
    }

    public function ajaxDeleteCallPlan()
    {
        $result = $this->model->deleteCallPlan();
        echo $result ? "success" : $result;
    }

    public function ajaxRecall()
    {
        $result = $this->model->updateRecall();
        echo $result ? "success" : $result;
    }

    public function ajaxSuspendSwitch()
    {
        return $this->model->updateSuspendSwitch();
    }

    public function ajaxMaxRoutingCalls()
    {
        $result = $this->model->updateMaxRoutingCalls();
        echo $result ? "success" : $result;
    }

    public function ajaxMaxCalls()
    {
        $result = $this->model->updateMaxCalls();
        echo $result ? "success" : $result;
    }

    public function ajaxcCallWaitingTime()
    {
        $result = $this->model->updateCallWaitingTime();
        echo $result ? "success" : $result;
    }

    public function ajaxcPlanDistribution()
    {
        $result = $this->model->updatePlanDistribution();
        echo $result ? "success" : $result;
    }

    public function ajaxUseState()
    {
        return $this->model->updateUseState();
    }

    public function downloadCalledCount()
    {
        $this->model->getDownloadCalledCount();
        $this->fileDownload($this->model->filePath);
    }

    private function fileDownload($filePath)
    {
        $fileName = end(explode("/", $filePath));
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

    public function downloadWaitCall()
    {
        $this->model->getDownloadWaitCall();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadCalloutCount()
    {
        $this->model->getDownloadCalloutCount();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadCallSwitchCount()
    {
        $this->model->getDownloadCallSwitchCount();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadCallConCount()
    {
        $this->model->getDownloadCallConCount();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadFaild()
    {
        $this->model->getDownloadFaild();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadMissed()
    {
        $this->model->getDownloadMissed();
        $this->fileDownload($this->model->filePath);
    }
}

?>
