<?php
/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2018/2/3
 * Time: 上午7:58
 */

namespace comm;


class FileHandler
{
	static public function getSize ($path)
	{
		$fp = fopen($path, "rb");
		if (!$fp) {
			throw new Exception("Cannot read from file.");
		}
		flock($fp, LOCK_SH);
		rewind($fp);
		$fileSize = 0;
		$chunkSize = 1024 * 1024;
		while (!feof($fp)) {
			$readBytes = strlen(fread($fp, $chunkSize));
			$fileSize += $readBytes;
		}
		flock($fp, LOCK_UN);
		fclose($fp);

		return $fileSize;
	}
}
