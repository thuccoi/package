<?php

namespace module\Share\Model\Common;

use Doctrine\ODM\MongoDB\SoftDelete\Configuration;
use Doctrine\ODM\MongoDB\SoftDelete\UnitOfWork;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteManager;
use Doctrine\Common\EventManager;

trait EntityDefault {

    private $code;
    private $dm;
    private $evm;
    private $sdm;
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


        $sconfig = new Configuration();
        $this->evm = new EventManager();
        $this->sdm = new SoftDeleteManager($connect, $sconfig, $this->evm);
    }

}
