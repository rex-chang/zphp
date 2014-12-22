<?php
/**
 * author: shenzhe
 * Date: 13-6-17
 * 公用方法类
 */

namespace ZPHP\Common;

use lib\Common;

class Utils
{

    /**
     * 判断是否ajax方式
     * @return bool
     */
    public static function isAjax()
    {

        if (!empty($_REQUEST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
            return true;
        }
        return false;
    }

    /**
     * Debug
     */
    public static function debug()
    {
        if (count(func_num_args())) {
            $args = func_get_args();

            echo PHP_EOL, 'debug begin', PHP_EOL, 'execuete File:' . debug_backtrace()[0]['file'] . PHP_EOL;
            echo 'execuete Line:' . debug_backtrace()[0]['line'] . PHP_EOL;
            foreach ($args as $var) {
                if (!empty($var) && !is_array($var)) {
                    echo var_export($var) . PHP_EOL;
                } else {
                    echo json_encode($var), PHP_EOL;
                }
            }
            echo 'end', PHP_EOL;

        }

    }

    public static function writeLog($fileName, $data)
    {
        $handle = fopen('/alidata1/log/' . $fileName, 'a+');
        $data = Common::getMicroTime() . "|||" .$data;
        fwrite($handle, $data . PHP_EOL);
        fclose($handle);
    }

}
