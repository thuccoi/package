<?php

namespace module\Assignment\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_assignment", 
 *   collection="PermissionLogs"
 * )  
 */
class PermissionLog extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldLogDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $permision_id;

    public function __construct($permision_id, $metatype = "", $message = "") {
        $this->initLog($metatype, $message);
        $this->permision_id = $permision_id;
    }

    public function release() {
        $obj = $this->exportLog();
        $obj->permision_id = $this->getPermissionId();

        return $obj;
    }

    //app id
    public function setPermissionId($permision_id) {
        $this->permision_id = $permision_id;
        return $this;
    }

    public function getPermissionId() {
        return $this->permision_id;
    }

}
