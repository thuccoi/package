<?php

namespace system\Database;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

class DoctrineMongo {

    public static function connect($loader, $config) {
        if (!isset($config['doctrine_mongo'])) {
            return null;
        }

        if (!isset($config['doctrine_mongo']['connect'])) {
            echo "Not found config connect doctrine_mongo in system config";
            exit;
        }

        if (!isset($config['doctrine_mongo']['connect']['host'])) {
            echo "Not found config host connect doctrine_mongo in system config";
            exit;
        }

        if (!isset($config['doctrine_mongo']['connect']['port'])) {
            echo "Not found config port connect doctrine_mongo in system config";
            exit;
        }

        //new config
        $config_dm_system = $config['doctrine_mongo'];

        $uri = 'mongodb://' . $config['doctrine_mongo']['connect']['host'] . ':' . $config['doctrine_mongo']['connect']['port'];

        if (isset($config['doctrine_mongo']['connect']['username']) && $config['doctrine_mongo']['connect']['username']) {
            if (!isset($config['doctrine_mongo']['connect']['password'])) {
                echo "Not found config password connect doctrine_mongo in system config";
                exit;
            }

            if (!isset($config['doctrine_mongo']['connect']['db_oauth'])) {
                echo "Not found config db_oauth connect doctrine_mongo in system config";
                exit;
            }

            $uri = 'mongodb://' . $config['doctrine_mongo']['connect']['username'] . ':' . $config['doctrine_mongo']['connect']['password'] . '@' . $config['doctrine_mongo']['connect']['host'] . ':' . $config['doctrine_mongo']['connect']['port'] . '/' . $config['doctrine_mongo']['connect']['db_oauth'];
        }

        $client = new \MongoDB\Client($uri, [], [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array',
            ],
        ]);


        AnnotationRegistry::registerLoader([$loader, 'loadClass']);

        //connect database
        $configdm = new Configuration();
        $configdm->setProxyDir($config_dm_system['data']['Proxies']);
        $configdm->setProxyNamespace('Proxies');
        $configdm->setHydratorDir($config_dm_system['data']['Hydrators']);
        $configdm->setHydratorNamespace('Hydrators');

        $configdm->setDefaultDB($config_dm_system['connect']['db_default']);

        //meta annotation driver
        foreach ($config_dm_system['map'] as $val) {
            $configdm->setMetadataDriverImpl(AnnotationDriver::create($val));
        }

        $dm = DocumentManager::create($client, $configdm);

        return $dm;
    }

}
