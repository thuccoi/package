<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_account", 
 *   collection="AppLogs"
 * )  
 * @ODM\HasLifecycleCallbacks
 */
class AppLog extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldLogDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $app_id;

    public function __construct($app_id, $metatype = "", $message = "") {
        $this->initLog($metatype, $message);
        $this->app_id = $app_id;
    }

    public function release() {
        $obj = $this->exportLog();
        $obj->app_id = $this->getAppId();

        return $obj;
    }

    //app id
    public function setAppId($app_id) {
        $this->app_id = $app_id;
        return $this;
    }

    public function getAppId() {
        return $this->app_id;
    }

}
