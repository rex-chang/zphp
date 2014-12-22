<?php

/**
 * User: Rex
 * Date: 13-6-17
 * 协议
 */

namespace ZPHP\Protocol\Adapter;

use ZPHP\Common\Utils;
use ZPHP\Core\Config;
use ZPHP\Common\MessagePacker;
use ZPHP\Protocol\IProtocol;
//use \ZPHP\Serialize\Adapter\Msgpack as SMsgPack;
use ZPHP\Cache\Factory as ZCache,
    \ZPHP\Serialize\Factory as ZSerialize,
    ZPHP\Core\Config as ZConfig;


class PPack implements IProtocol
{

    private $_ctrl = 'main';
    private $_method = 'main';
    private $_params = array();
    private $_buffer = array();
    private $fd;
    private $_data;


    /**
     * client包格式： writeString(json_encode(array("a"='main/main',"m"=>'main', 'k1'=>'v1')));
     * server包格式：包总长+数据(json_encode)
     * @param $_data
     * @return bool
     */
    public function parse($_data)
    {
//        echo 321321321;
        $this->_ctrl = Config::getField('project', 'default_ctrl_name', 'main\\main');
        $this->_method = Config::getField('project', 'default_method_name', 'main');
        if (empty($this->_cache)) {
            $config = ZConfig::getField('cache', 'locale', array());

            $this->_cache = ZCache::getInstance('Redis', $config);
        }
        if (!empty($cacheData)) {
            $_data = $cacheData . $_data;
            $this->_cache->delete($this->fd);
        }
        if (empty($_data)) {
            return false;
        }
        $packData = new MessagePacker($_data);
        $packLen = $packData->readInt16();
        $dataLen = $packLen;
        if ($packLen > $dataLen) {
            $this->_cache->set($this->fd, $_data);
            return false;
        } elseif ($packLen < $dataLen) {
            $this->_cache->set($this->fd, \substr($_data, $packLen, $dataLen - $packLen));
        }
        //read CMD
        $packData->resetOffset(2);
        $this->_cmd = $packData->readInt16();
        $this->_sid = $packData->readInt();
        $this->_rid = $packData->readInt();

        $params = $packData->getBuffer();
        $this->_params = ZSerialize::unserialize('Msgpack', $params);
//        $testObj = new \stdClass();
//        $testObj->num = 123;
        $testObj = array('num' => 123);
        $this->_data = array('pcount' => count($this->_params),
            'time' => time(),
            'obj' => $testObj
        );
//        var_dump('params:', $this->_params);
        $this->_ctrl = Config::getField('cmdList', $this->_cmd);
        if (!$this->_ctrl)
            return false;
        $this->_ctrl .= 'Ctrl';
        $this->_method = Config::getField('methodList',
            $this->_cmd)[$this->_params['type']];

        if (!$this->_method)
            return false;
        return $this->_params;
    }

    public function getData()
    {
        $mpackData = ZSerialize::serialize('Msgpack', $this->_data);
//        SMsgPack::serialize($this->_data);
        $mpackr = new MessagePacker();
        $packLen = strlen($mpackData) + 10;
        $mpackr->writeInt16($packLen);
        $mpackr->writeInt16(1);
        $mpackr->writeInt(2);
        $mpackr->writeInt(3);
        $mpackr->writeBinary($mpackData, 0, 0);
        $data = $mpackr->getData();
        $this->_data = null;
        return $data;
    }

    public function setFd($fd)
    {
        $this->fd = $fd;
    }

    public function getFdBuffer($fd)
    {
        return !empty($this->_buffer[$fd]) ? $this->_buffer[$fd] : false;
    }

    public function getCtrl()
    {
        return $this->_ctrl;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function display($model)
    {
//        $data = array();
//        if (is_array($model)) {
//            $data = $model;
//        } else {
//            $data['data'] = $model;
//        }
//        $data['fd'] = $this->fd;
//        $this->_data = $data;
//        return $this->getData();
        return $model;
    }

    public function sendMaster(array $_params = null)
    {
        if (!empty($_params)) {
            $this->_data = $this->_data + $_params;
        }
        $host = Config::getField('socket', 'host');
        $port = Config::getField('socket', 'port');
        $client = new ZSClient($host, $port);
        $client->send($this->getData());
    }

    public function packData($cmd, array $data = array(), $send = 8, $recv = 9)
    {
//        if ($cmd != 1)
//            Utils::debug('sendData:', 'cmd:', $cmd, $data);

        $mpackData = ZSerialize::serialize('Msgpack', $data);
        $mpackr = new MessagePacker();
        $packLen = strlen($mpackData) + 10;
        $mpackr->writeInt16($packLen);
        $mpackr->writeInt16($cmd);
        $mpackr->writeInt($send);
        $mpackr->writeInt($recv);
        $mpackr->writeBinary($mpackData, 0, 0);
        return $mpackr->getData();
    }

    public function getFd()
    {
        return $this->fd;
    }

}
