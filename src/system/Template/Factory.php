<?php

namespace system\Template;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

final class Factory implements \system\Template\ControllerInterface {

    public function __invoke($loader, $requestName, \system\Router $router, \system\Helper\Code $code, array $config = null, array $options = null) {
        
        //connect database
        $dm = $this->connectDm($loader, $config);

        return new $requestName($dm, $router, $code, $config, $options);
    }

    public function connectDm($loader, $config) {

        //get config database
        if (!isset($config['doctrine_mongo'])) {
            throw new \RuntimeException('Not find config doctrine_mongo in system config');
        }

        $config_dm = $config['doctrine_mongo'];

        //set map namespace
        foreach ($config_dm['map'] as $key => $val) {
            //add namespace
            $loader->add($key, $val);
        }

        if (!isset($config_dm['connect'])) {
            throw new \RuntimeException('Not find config connect in doctrine_mongo');
        }

        if (!isset($config_dm['connect']['host'])) {
            throw new \RuntimeException('Not find config host connect in doctrine_mongo');
        }

        if (!isset($config_dm['connect']['port'])) {
            throw new \RuntimeException('Not find config port connect in doctrine_mongo');
        }

        $stringconnect = $config_dm['connect']['host'] . ":" . $config_dm['connect']['port'];
        //setconfig connect
        if (isset($config_dm['connect']['username']) && $config_dm['connect']['username']) {

            if (!isset($config_dm['connect']['password'])) {
                throw new \RuntimeException('Not find config password connect in doctrine_mongo');
            }

            $stringconnect = $config_dm['connect']['username'] . ":" . $config_dm['connect']['password'] . "@" . $stringconnect;
        }

        //connect database
        $connection = new Connection($stringconnect);

        $configdm = new Configuration();

        if (!isset($config_dm['data']['Proxies'])) {
            throw new \RuntimeException('Not find config dir Proxies data in doctrine_mongo');
        }

        //set data proxies and hydrators
        $configdm->setProxyDir($config_dm['data']['Proxies']);
        $configdm->setProxyNamespace('Proxies');


        if (!isset($config_dm['data']['Hydrators'])) {
            throw new \RuntimeException('Not find config dir Hydrators data in doctrine_mongo');
        }


        $configdm->setHydratorDir($config_dm['data']['Hydrators']);
        $configdm->setHydratorNamespace('Hydrators');


        if (!isset($config_dm['connect']['db_default'])) {
            throw new \RuntimeException('Not find config db_default connect in doctrine_mongo');
        }

        //set db default
        $configdm->setDefaultDB($config_dm['connect']['db_default']);

        //meta annotation driver
        foreach ($config_dm['map'] as $val) {
            $configdm->setMetadataDriverImpl(AnnotationDriver::create($val));
        }

        AnnotationDriver::registerAnnotationClasses();

        $dm = DocumentManager::create($connection, $configdm);
        
        return $dm;
    }

}
