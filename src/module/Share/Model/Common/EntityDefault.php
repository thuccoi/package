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

}
