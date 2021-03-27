<?php

use comm\DB;

class DownloadFile_Model extends JModel
{
    public function getRecordDownload()
    {
        $emps = [$this->choice];
        $sub_emp = $this->getSubEmp($this->choice);
        foreach ($sub_emp as $user) {
            $emps[] = $user["UserID"];
        }
        $db = DB::table('CallOutCDR')->select('UserID', 'CallStartBillingDate', 'RecordFile');
        $db->whereBetween('cast(CallStartBillingDate as datetime)',
            [$this->callStartBillingDate, $this->callStopBillingDate]);
//        $db->andWhere('RecordFile', '<>', '');
        $db->addRaw("and RecordFile <> ''");
        $db->whereIn('UserID', $emps);
        if (!empty($this->extensionNo)) {
            $db->andWhere('ExtensionNo', $this->extensionNo);
        }
        if (!empty($this->orgCalledID)) {
            $db->andWhere('OrgCalledID', $this->orgCalledID);
        }
        if (!empty($this->callDuration)) {
            $db->andWhere('CallDuration', $this->durationCondition == "within" ? "<=" : ">", $this->callDuration);
        }
        $result = $db->get();
        if (!count($result)) {
            $this->warning = "條件範圍，找不到任何資料！！";//$dba->mergeSQL($sql,$params).
            return;
        }
        //include_once $this->base["comm_dir"]."phpzip.php";
        //$zip = new PHPZip("test.zip");
        //$tmpFolder = "tmp/".$this->session["choice"]."/";
        //@mkdir("tmp");
        //$this->delete_files($tmpFolder);
        //@mkdir($tmpFolder);
        //@mkdir("download");
        $this->targetFile = $this->session["choice"] . "RecordFile.zip";
        $zip = new ZipArchive();
        if ($zip->open($this->targetFile, ZIPARCHIVE::CREATE) !== true) {
            throw new \Exception("Cannot open <$this->targetFile>\n", 500);
        }
        $file_count = 0;
        foreach ($result as $data) {
            $userId = $data["UserID"];
            $connectDate = date("Ymd", strtotime($data["CallStartBillingDate"]));
            $fileName = $data["RecordFile"];
            $filePath = "D:\\Recording\\{$userId}\\{$connectDate}\\{$fileName}";
            if (file_exists($filePath) && !is_dir($filePath)) {
                //copy($filePath,$tmpFolder.$fileName);
                //$zip->addFile($tmpFolder.$fileName, basename($fileName));
                $zip->addFile($filePath, basename($fileName));
//				$zip->setCompressionIndex($file_count, ZipArchive::CM_STORE); // 不壓縮，但時間好像沒什麼差
                $file_count++;
            }
        }
        $zip->close();
        @mkdir($this->base['record']);
        $this->targetPath = $this->base['record'] . $this->targetFile;
        rename($this->base["root_folder"] . $this->targetFile, $this->targetPath);
    }
}

?>﻿
