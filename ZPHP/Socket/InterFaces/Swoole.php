<?php

namespace ZPHP\Socket\InterFaces;
/**
 *  Swoole扩展实现接口
 */
interface Swoole {
    /**
     * 
     */
    public function onTimer();
    
    public function onWorkerStart();
    public function onWorkerStop();
    public function onMasterConnect();
    public function onMasterClose();
    public function onTask();
    public function onFinish();
    public function onWorkerError();
    public function onManagerStart();
    public function onManagerStop();
    
    
}
