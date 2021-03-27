<?php

class AdCallSetting_Controller extends JController
{
    /**
     * 新增廣告群乎
     * @return $this|void
     */
    public function adCallSchedule()
    {

        $model = $this->model;
        if ($model->submit) {
            if ($model->status == "delete") {
                $model->deleteAdCallSchedule();
            } else {
                $model->postAdCallSchedule();
            }
        }
        $model->getAdCallSchedule();
        $this->getConcurrentCallsSelect();
        $this->voiceFileList = $this->getVoiceFileList();
        return parent::render();
    }

    /**
     * 取得廣告群呼音檔資料夾內所有檔案名
     * @return array
     */
    private function getVoiceFileList()
    {
        return lib\VoiceRecord::getFilesName($this->model->session["choice"]);
    }

    /**
     * 新增廣告群乎單筆修改
     * @return $this|void
     */
    public function adCallScheduleModify()
    {
        $model = $this->model;
        if ($model->submit) {
            if ($model->updateAdCallScheduleDetail()) {
                parent::redirect("adCallSetting/adCallSchedule");
            }
        }
        $model->getAdCallScheduleDetail();
        $this->getConcurrentCallsSelect();
        $this->voiceFileList = $this->getVoiceFileList();
        return parent::render();
    }

    /**
     * 廣告群乎音檔管理
     * @return $this|void
     */
    public function voiceFileManage()
    {
        $model = $this->model;
        if ($model->submit) {
            $model->uploadFileAndConvert($model->modifyName);
        }
        $this->voiceFileList = $this->getVoiceFileList();
        return parent::render();
    }

    /**
     * 廣告群乎音檔下載
     * @return $this|void
     */
    public function downloadVoiceFile()
    {
        $filePath = $this->base["voiceManage"] . $this->model->session['choice'] . "/" . $this->model->fileName;
        if (file_exists($filePath)) {
            $this->downloadFile($filePath);
        } else {
            echo "<script>alert('找不到{$this->model->fileName}檔案，請刪除後重新上傳！');history.go(-1);</script>";
        }
    }

    /**
     * 廣告群乎音檔刪除
     * @return $this|void
     */
    public function ajaxDeleteVoiceFile()
    {
        $this->model->deleteVoiceFile($this->model->fileName);
        echo json_encode($this->getVoiceFileList());
    }

    /**
     * 廣告群乎音檔全部刪除
     * @return $this|void
     */
    public function ajaxDeleteAllVoiceFile()
    {
        $this->model->deleteAllVoiceFile();
        echo json_encode($this->getVoiceFileList());
    }

    ////////////////////////////////////////////////////////////////////////////////

    /**
     * 廣告群乎狀態
     * @return $this|void
     */
    public function adCallStatus()
    {
        $this->model->getAdCallStatus();
        $this->getConcurrentCallsSelect();
        return parent::render();
    }

    /**
     * 廣告群乎狀態api
     * @return $this|void
     */
    public function ajaxAdCallStatusContent()
    {
        $this->model->getAjaxAdCallStatusContent();
        $tmp_model = new stdClass();
        $tmp_model->data3 = $this->model->data3;
        $tmp_model->waitExtensionNoCount = $this->model->waitExtensionNoCount ?? "";
        $tmp_model->extensionNoCount = $this->model->extensionNoCount ?? "";
        $tmp_model->balance = $this->model->balance;
        echo json_encode($tmp_model);
    }

    public function ajaxAdSuspendSwitch()
    {
        $result = $this->model->updateAdSuspend();
        echo $result ? "success" : $result;
    }

    public function ajaxChgConcurrentCalls()
    {
        $result = $this->model->updateConcurrentCalls();
        echo $result ? "success" : $result;
    }

    public function ajaxUseState()
    {
        return $this->model->updateUseState();
    }

    public function ajaxDeleteAdPlan()
    {
        $result = $this->model->deleteAdPlan();
        echo $result ? "success" : $result;
    }

    public function ajaxAdNote()
    {
        $result = $this->model->updateUserAdNote();
        echo $result ? "success" : $result;
    }

    /**
     * 廣告呼叫狀態下載
     * 筆數
     */
    public function downloadCalledCount()
    {
        $this->model->getDownloadCalledCount();
        $this->arrayDownload($this->model->data, date("Y-m-d", time()) . "_{$this->model->session["choice"]}.txt");
    }

    /**
     * 待發
     */
    public function downloadWaitCall()
    {
        $this->model->getDownloadWaitCall();
        // echo json_encode($this->model->data);
        $this->arrayDownload($this->model->data, date("Y-m-d", time()) . "_{$this->model->session["choice"]}.txt");
    }

    /**
     * 執行
     */
    public function downloadCalloutCount()
    {
        $this->model->getDownloadCalloutCount();
        $this->arrayDownload($this->model->data, date("Y-m-d", time()) . "_{$this->model->session["choice"]}.txt");
    }

    /**
     * 接聽數
     */
    public function downloadCallConCount()
    {
        $this->model->getDownloadCallConCount();
        $this->arrayDownload($this->model->data, date("Y-m-d", time()) . "_{$this->model->session["choice"]}.txt");
    }

    /**
     * 未接通待發
     */
    public function downloadUnRecieveWaitCall()
    {
        $this->model->getDownloadUnRecieveWaitCall();
        $this->arrayDownload($this->model->data, date("Y-m-d", time()) . "_{$this->model->session["choice"]}.txt");
    }

    /**
     * 未接通
     */
    public function downloadCallUnavailable()
    {
        $this->model->getDownloadCallUnavailable();
        $this->arrayDownload($this->model->data, date("Y-m-d", time()) . "_{$this->model->session["choice"]}.txt");
    }

    ////////////////////////////////////////////////////////////////////////////////

    /**
     * 廣告通聯查詢
     * @return $this|void
     */
    public function adCommunicationSearch()
    {
        return parent::render('adCommunicationSearch2');
    }

    public function downloadAdCommunicationSearch()
    {

        $this->model->getAdCommunicationSearch();
        //echo json_encode($this->model->data);
        $this->exportExcel("AdCommunication_" . $this->model->session["choice"] . ".xls");
        $title = ["ID", "目的碼", "撥打時間", "接通時間", "掛斷時間", "通話秒數", "按鍵", "傳真"];
        $colspans = count($title);
//        $title = array_map(function ($data) {
//            return iconv("UTF-8","Big5",$data);
//        }, $title);
//        echo join("\t",$title)."\n";
//        array_map(function ($data, $index) {
//            echo join("\t",[$index+1,  "=T(\"{$data['OrgCalledId']}\")" , $data['CalledCalloutDate'], $data[CallStartBillingDateTime], $data[CallLeaveDateTime], $data[CallDuration]])."\n";
//        }, $this->model->data, array_keys($this->model->data));
        $html = "
        <html>
            <head>
                <meta charset=\"UTF-8\">
            </head>
            <body>
                <table border=\"1\">";
        $html .= "<tr>" . join("", array_map(function ($data) {
                return "<th>" . $data . "</th>";
            }, $title)) . "</tr>";
        $html .= join("", array_map(function ($data, $index) {
            return
                "<tr>" .
                join("",
                    array_map(
                        function ($subData) {
                            return "<td>" . $subData . "</td>";
                        },
                        [
                            $index + 1,
                            "'" . $data['OrgCalledId'],
                            $data['CalledCalloutDate'],
                            $data['CallStartBillingDateTime'],
                            $data['CallLeaveDateTime'],
                            $data['CallDuration'],
                            $data['RecvDTMF'],
                            $data['FaxCall'] ? '是' : '否'
                        ]
                    )
                ) .
                "</tr>";
        }, $this->model->data, array_keys($this->model->data)));
        $html .= "<tr>
                    <td colspan={$colspans}>總時間：{$this->model->totalTime}</td>
                  </tr>
                  <tr>
                    <td colspan={$colspans}>總通數：{$this->model->count}</td>
                  </tr>
                  <tr>
                    <td colspan={$colspans}>總接通數：{$this->model->totalConnected}</td>
                  </tr>";
        $html .= "
                </table>
            </body>
        </html>";
        echo $html;
    }

    public function ajaxAdCommunicationSearch2()
    {
        $this->model->getAdCommunicationSearch2();
        echo json_encode([
            "count"          => $this->model->count,
            "totalTime"      => $this->model->totalTime,
            "totalMoney"     => $this->model->totalMoney,
            "totalConnected" => $this->model->totalConnected,
            "data"           => $this->model->data
        ]);
    }
}

?>
