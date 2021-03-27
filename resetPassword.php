<?php

require('vendor/autoload.php');

use comm\DB;
use comm\DBA;
use lib\Hash;

// .env start
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');
//echo getenv('DB_USER');

//use Crypt;

//$dba = new DBA();
//$dba->connect();
//print_r($dba);
foreach (DB::table('SysUser')->select('UserID','UserPassword','BackupPassword')->get() as $user)
{
//	echo $user["UserID"] . '^^' . \Crypt::decrypt($user["UserPassword"]).'<br>';
//	DB::table('SysUser')->update([
//		'BackupPassword' => \Crypt::decrypt($user["UserPassword"])
//	])->where([
//		[ 'UserID', $user["UserID"] ]
//	])->exec();

//	var_dump(Hash::encode('1234'));

//	$crypt = Hash::encode($user["BackupPassword"]);
//	echo $user["UserID"] . '^^' . $user["BackupPassword"]. '^^' . $crypt . '^^' . Hash::decode($crypt).'<br>';
//	echo DB::table('SysUser')->update([
//		'BackupPassword' => $crypt
//	])->where([
//		[ 'UserID', $user["UserID"] ]
//	])->exec();
//	->export()."<br><br>";
}

// zrQphjjueuDg8T2tgscHFBzunl5XNTwUY61YipY7c/Tr2zYsJIMEthYgV8y3q+WusIlt7BG0pA
//var_dump(Hash::encode('miyavi993701'));
//echo "<br>";
//var_dump('xuF5iTLuf+v1szGr1cAHGBvUkggdJSNKOK16zZUtRdTxwEAgK4A9og');
//echo "<br>";
//var_dump(DB::table('SysUser')->select()->where([
//	['UserID', 'user81'],
//    ['BackupPassword', Hash::encode('miyavi993701')]
//])->get());
//user81^^miyavi993701
?>
