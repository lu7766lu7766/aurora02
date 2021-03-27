<?php
/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2017/5/25
 * Time: 下午10:44
 */

namespace comm;

use Composer\Config;

class Http
{
	/**
	 * @param $filePath 實體路徑
	 */
	static public function download ($filePath)
	{
//		$fileSize = self::get("http://127.0.0.1:60/GetFileSize.atp?File={$filePath}");
		$fileSize = FileHandler::getSize($filePath) + 10;
		$sizeTxt = self::formatSizeUnits($fileSize);
//		if ($fileSize > 800000000) return self::download2($filePath);
//		$fileSize += ceil($fileSize / 800000000) * 10 + 10; // 800MB+10
		$fileName = basename($filePath);
		$file_extension = strtolower(substr(strrchr($fileName, "."), 1));
		$ctype = self::getCType($file_extension);
//		die( "{$fileSize}^^" . filesize($filePath) . "^^{$filePath}^^{$ctype}" );
		Header("Content-type: {$ctype}");
		Header("Content-Disposition: attachment; filename=" . $fileName);
		Header('Content-Length: ' . $fileSize);
		ob_clean();
		ob_end_flush();
		readfile($filePath);
		unlink($filePath);
		exit;
	}

	static private function formatSizeUnits ($bytes)
	{
		if ($bytes >= 1099511627776) {
			$bytes = number_format($bytes / 1099511627776, 2) . ' TB';
		} elseif ($bytes >= 1073741824) {
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		} elseif ($bytes >= 1048576) {
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		} elseif ($bytes >= 1024) {
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		} elseif ($bytes > 1) {
			$bytes = $bytes . ' bytes';
		} elseif ($bytes == 1) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	static private function getCType ($ext)
	{
		switch ($ext) {
			case "pdf":
				$ctype = "application/pdf";
				break;
			case "exe":
			case "txt":
			case "csv":
				$ctype = "application/octet-stream";
				break;
			case "zip":
				$ctype = "application/zip";
				break;
			case "doc":
				$ctype = "application/msword";
				break;
			case "xls":
				$ctype = "application/vnd.ms-excel";
				break;
			case "ppt":
				$ctype = "application/vnd.ms-powerpoint";
				break;
			case "gif":
				$ctype = "image/gif";
				break;
			case "png":
				$ctype = "image/png";
				break;
			case "jpeg":
			case "jpg":
				$ctype = "image/jpg";
				break;
			case "mp3":
				$ctype = "audio/mpeg";
				break;
			case "wav":
				$ctype = "audio/x-wav";
				break;
			case "mpeg":
			case "mpg":
			case "mpe":
				$ctype = "video/mpeg";
				break;
			case "mov":
				$ctype = "video/quicktime";
				break;
			case "avi":
				$ctype = "video/x-msvideo";
				break;
			//禁止下面幾種類型的檔案被下載
			case "php":
			case "htm":
			case "html":
				die( "Cannot be used for " . $ext . " files!" );
				break;

			default:
				$ctype = "application/force-download";
		}

		return $ctype;
	}

	static public function get ($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch);
		curl_close($ch);

		return $html;
	}

	static public function post ($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		// 以資料回傳，不是直接輸出內容
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch);
		curl_close($ch);

		return $html;
	}

	static public function download2 ($filePath)
	{
		global $config;
		$url = $config->base[ 'url' ] . str_replace($config->base[ "root_folder" ], "", $filePath);
		header("location:" . $url);
	}
}
