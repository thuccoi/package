<?php

namespace module\Assignment\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="RoleToMemberLogs", db="tami_assignment")
 */
class RoleToMemberLog extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldLogDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $role_id;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $member_id;

    public function __construct($role_id = "", $member_id = "", $metatype = "", $message = "", $app_id = "", $creator_id = "") {
        $this->initLog($metatype, $message, $app_id, $creator_id);
        $this->role_id = $role_id;
        $this->member_id = $member_id;
    }

    public function release() {
        $obj = $this->exportLog();
        $obj->role_id = $this->getRoleId();
        $obj->member_id = $this->getMemberId();

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

    //member id
    public function setMemberId($member_id) {
        $this->member_id = $member_id;
        return $this;
    }

    public function getMemberId() {
        return $this->member_id;
    }

}
