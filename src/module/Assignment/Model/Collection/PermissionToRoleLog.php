<?php

namespace module\Assignment\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="PermissionToRoleLogs", db="tami_assignment")
 */
class PermissionToRoleLog extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldLogDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $role_id;


    public function __construct($role_id = "" , $metatype = "", $message = "") {
        $this->initLog($metatype, $message);
        $this->role_id = $role_id;
    }

    public function release() {
        $obj = $this->exportLog();
        $obj->role_id = $this->getRoleId();

        return $obj;
    }

    //role id
    public function setRoleId($role_id) {
        $this->role_id = $role_id;
        return $this;
    }

    public function getRoleId() {
        return $this->role_id;
    }

}
