<?php

namespace module\Assignment\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_assignment", 
 *   collection="PermissionToRoles"
 * )  
 */
class PermissionToRole extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldDefault;

    /**
     * @ODM\ReferenceOne(targetDocument="module\Assignment\Model\Collection\Role", inversedBy="permissions")
     */
    private $role;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $permission;

    //role
    public function getRole() {
        return $this->role;
    }

    public function setRole(\module\Assignment\Model\Collection\Role $obj) {
        $this->role = $obj;
        return $this;
    }

    //permission
    public function getPermission() {
        return $this->permission;
    }

    public function setPermission($per) {
        $this->permission = $per;
        return $this;
    }

    public function release() {
        $obj = $this->export();

        return $obj;
    }

}
