<?php

class Env_Controller extends JController
{
	public function netSetting ()
	{
		return parent::render();
	}

	public function correctTime ()
	{
		return parent::render();
	}

	public function connectTest ()
	{
		return parent::render();
	}

	public function netRoute ()
	{
		return parent::render();
	}

	public function picUpload ()
	{
		return parent::render();
	}

	public function sysName ()
	{
		return parent::render();
	}

	public function ping ()
	{
		$domain = $this->model->domain;
		$ip = gethostbyname($domain);
		$times = 4;
		$i = 0;

		echo "PING $domain ($ip)<br>";

		while ($times-- > 0) {
			$starttime = microtime(true);
			$file = fsockopen($domain, 80, $errno, $errstr, 10);
			$stoptime = microtime(true);
			$status = 0;

			if (!$file) $status = -1;  // Site is down
			else {
				fclose($file);
				$status = ( $stoptime - $starttime ) * 1000;
				$status = floor($status);
			}
			echo "from 173.194.72.102: icmp_seq=" . ( $i++ ) . " time=$status ms<br>";
			sleep(1);
		}
		echo "--- $domain statistics ---";

	}
}

?>
