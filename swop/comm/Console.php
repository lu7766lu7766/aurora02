<?php

/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2018/1/2
 * Time: 上午11:33
 */
namespace comm;

class Console
{
	public static function log ($output)
	{
		echo "<script>console.log(`$output`)</script>\n";
	}

	public static function dd ($output)
	{
		echo "<pre>";
		print_r($output);
		echo "</pre>";
	}
}
