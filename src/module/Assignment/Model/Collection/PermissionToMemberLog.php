<?php

namespace module\Assignment\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="PermissionToMemberLogs", db="tami_assignment")
 */
class PermissionToMemberLog extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldLogDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $member_id;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $permission_id;

    public function __construct($member_id = "", $permission_id = "", $metatype = "", $message = "") {
        $this->initLog($metatype, $message);
        $this->member_id = $member_id;
        $this->permission_id = $permission_id;
    }

    public function release() {
        $obj = $this->exportLog();
        $obj->member_id = $this->getMemberId();
        $obj->permission_id = $this->getPermissionId();

        return $obj;
    }

    //member id
    public function setMemberId($member_id) {
        $this->member_id = $member_id;
        return $this;
    }

    public function getMemberId() {
        return $this->member_id;
    }

    //permission id
    public function setPermissionId($permission_id) {
        $this->permission_id = $permission_id;
        return $this;
    }

    public function getPermissionId() {
        return $this->permission_id;
    }

}
