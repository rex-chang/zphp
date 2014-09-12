<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 * Json view
 */


namespace ZPHP\View\Adapter;
use ZPHP\View\Base,
    ZPHP\Core\Config;

class Json extends Base
{
    public function display()
    {
        if (Config::get('server_mode') == 'Http') {
            $data = \json_encode($this->model);
            if(isset($_GET['jsoncallback'])) {
                \header('application/x-javascript; charset=utf-8');
                echo $_GET['jsoncallback'].'('.$data.')';
            } else {
                \header("Content-Type: application/json; charset=utf-8");
                echo $data;
            }
            
        } else {
        	echo \json_encode($this->model);
        }

        

    }


}
