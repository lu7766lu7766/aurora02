<?php
/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2019/2/4
 * Time: 上午10:41
 */

namespace lib;

class QueueSQL
{
    static function write($sql)
    {
        $file = fopen('queue.sql', 'a');
        fwrite($file, $sql);
        fclose($file);
    }

    static function exec()
    {
//sqlcmd -U dbUser -P dbPed -d dbName -i sqlFilepath
    }
}
