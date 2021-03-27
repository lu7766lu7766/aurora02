<?php

class DownloadFile_Controller extends JController
{
    public function __construct($base)
    {
        $this->checkLogin = false;
        parent::__construct($base);
    }

    public function recordFile()
    {
        $model = $this->model;
        $fileName = $model->fileName;
        $userId = $model->userId;
        $connectDate = $model->connectDate;
        $filePath = "D:\\Recording\\{$userId}\\{$connectDate}\\{$fileName}";
        $newFilePath = $this->base["download"] . $fileName;
        copy($filePath, $newFilePath);
//        $fileSize = filesize($newFilePath);
//        header('Pragma: public');
//        header('Expires: 0');
//        header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//        header('Cache-Control: private', false);
//        header('Content-Type: application/octet-stream');
//        header('Content-Length: ' . $fileSize);
//        header('Content-Disposition: attachment; filename="' . $fileName . '";');
//        header('Content-Transfer-Encoding: binary');
//        readfile($newFilePath);
//        unlink($newFilePath);
//        die($newFilePath);
        \comm\Http::download2($newFilePath);
//        unlink($newFilePath);
//        $targetFile = $fileName . '.zip';
//        $zip = new ZipArchive();
//        if ($zip->open($targetFile, ZIPARCHIVE::CREATE) !== true) {
//            throw new \Exception("Cannot open <$this->targetFile>\n", 500);
//        }
//        $zip->addFile($newFilePath, basename($newFilePath));
//        $zip->close();
//        $targetPath = $this->base['record'] . $targetFile;
//        rename($this->base["root_folder"] . $targetFile, $targetPath);
//        \comm\Http::download($targetPath);
    }

    public function recordFilesToZip()
    {
        $model = $this->model;
        $model->getRecordDownload();
        if ($model->warning != "") {
            echo "<script>";
            echo "alert('$model->warning');";
            echo "window.close();";
            echo "</script>";
            die("");
        }
        \comm\Http::download($model->targetPath);
    }
}

?>
