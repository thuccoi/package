<?php

namespace module\Share\Model\Common;

trait EntityDefault {

    private $code;
    private $dm;
    private $config;

    //set properties code
    public function __construct($connect, \system\Helper\Code $code, $config) {

        // $dm is a DocumentManager instance we should already have
        $this->init($connect, $code, $config);
    }

    public function init($connect, \system\Helper\Code $code, $config) {
        $this->code = $code;
        $this->dm = $connect;
        $this->config = $config;
    }

    //config
    public function setConfig($config) {
        $this->config = $config;
        return $this;
    }

    public function getConfig() {
        return $this->config;
    }

    //code
    public function setCode(\system\Helper\Code $code) {
        $this->code = $code;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    //connect
    public function setConnect($connect) {
        $this->dm = $connect;
        return $this;
    }

    public function getConnect() {
        return $this->dm;
    }

}
