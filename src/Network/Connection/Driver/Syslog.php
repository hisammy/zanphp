<?php
/**
 * Created by IntelliJ IDEA.
 * User: winglechen
 * Date: 16/4/5
 * Time: 11:36
 */

namespace Zan\Framework\Network\Connection\Driver;

use swoole_client as SwooleClient;
use Zan\Framework\Contract\Network\Connection;

class Syslog extends Base implements Connection
{
    private $clientCb;
    private $postData;
    protected $isAsync = true;

    public function init()
    {
        //set callback
        $this->getSocket()->on('connect', [$this, 'onConnect']);
        $this->getSocket()->on('receive', [$this, 'onReceive']);
        $this->getSocket()->on('close', [$this, 'onClose']);
        $this->getSocket()->on('error', [$this, 'onError']);
    }

    public function send($log)
    {
        $this->postData = $log . "\n";
        $this->getSocket()->send($this->postData);
        $this->release();
    }

    public function onConnect($cli)
    {
        $this->release();
    }

    public function onClose(SwooleClient $cli)
    {
        $this->close();
    }

    public function onReceive(SwooleClient $cli, $data)
    {
        $this->release();
        call_user_func($this->clientCb, $data);
    }

    public function onError(SwooleClient $cli)
    {
        $this->close();
    }

    public function setClientCb(callable $cb)
    {
        $this->clientCb = null;
    }

    protected function closeSocket()
    {
        return true;
    }
}
