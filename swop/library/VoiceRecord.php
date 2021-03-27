<?php

/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2018/2/8
 * Time: 下午12:47
 */
namespace lib;

class VoiceRecord
{
	static public $sourceExt = '.wav';
	static public $davidExt = '.g729';
	static public $fileRoot = "C:\\Program Files (x86)\\AssistorCore\\VoiceFiles\\Ad\\";

	static public function uploadFile ($userID, $fieldName)
	{
		if (file_exists($_FILES[ $fieldName ][ 'error' ])) return -1;
		if (!file_exists($_FILES[ $fieldName ][ 'tmp_name' ])) return false;

		if ($fieldName == 'voiceFile') {
			return self::uploadLocal($userID, $fieldName);
		} else if ($fieldName == 'voiceFile2') {
			return self::uploadByApi($userID, $fieldName);
		}

	}

	static private function uploadLocal ($userID, $fieldName)
	{
		global $config;

		$dir = $config->base[ 'download' ];
		@mkdir($dir, 0777);
		@mkdir($config->base[ 'voiceManage' ], 0777);
		$target_folder = $config->base[ 'voiceManage' ] . $userID . "/";
		@mkdir($target_folder, 0777);

		$fileName = iconv('utf-8', 'big5', $_FILES[ $fieldName ][ 'name' ]);
		move_uploaded_file($_FILES[ $fieldName ][ 'tmp_name' ], $dir . $fileName);
		copy($dir . $fileName, $target_folder . $fileName);

		$url = "http://127.0.0.1:60/ConvertFile.atp?User={$userID}&File={$fileName}";
		\comm\Http::get($url);

		return $fileName;
	}

	static private function uploadByApi ($userID, $fieldName)
	{
		$fileName = iconv('utf-8', 'big5', $_FILES[ $fieldName ][ 'name' ]);

		$url = "http://sms.nuage.asia/putwavfile.php";
		ob_start();
		$fileData = file_get_contents($_FILES[ $fieldName ][ 'tmp_name' ]);
//		$fileData = ob_get_contents();
		ob_end_clean();

		$data = [
			'name'   => $fileName,
			'owner'  => $userID,
			'string' => base64_encode($fileData)
		];

		$res = \comm\Http::post($url, $data);
//		\comm\Console::dd(json_decode($res, true));
//		echo $res . "^^";
//		die();

		return $fileName;
	}

	static public function getFilesName ($userID)
	{
		$res = [ ];
		$files = self::getFiles($userID);
		foreach ($files as $file) {
			if (substr($file, -1) == '/')
				return;
			else
				$res[] = iconv('big5', 'utf-8', self::getFileNameWithoutExt(basename($file)) . self::$sourceExt);
		}

		return $res;
	}

	static private function getFiles ($userID)
	{
		return glob(self::getCurrentPath($userID) . '*', GLOB_MARK);
	}

	static private function getCurrentPath ($userID)
	{
		return self::$fileRoot . $userID . "\\";
	}


	static private function getFileNameWithoutExt ($fileName)
	{
		return strtr($fileName, [ self::$davidExt => '', self::$sourceExt => '' ]);
	}

	static public function delFile ($userID, $fileName)
	{
		global $config;

		$fileName = self::getFileNameWithoutExt($fileName);
		$big5fileName = iconv('utf-8', 'big5', $fileName);
		$davidFile = $big5fileName . self::$davidExt;
		$jacFile = $big5fileName . "." . self::$sourceExt;

		$delFilePath = self::getCurrentPath($userID) . $davidFile;
		unlink($delFilePath);

		$targetPath = $config->base[ 'voiceManage' ] . $userID . "/" . $jacFile;
		@unlink($targetPath);
	}
}
