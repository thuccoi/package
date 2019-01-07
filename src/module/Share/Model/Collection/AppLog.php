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

    public function __construct($metatype = "", $message = "") {
        $this->initLog($metatype, $message);
    }

    public function release() {
        $obj = $this->exportLog();

        return $obj;
    }

}
