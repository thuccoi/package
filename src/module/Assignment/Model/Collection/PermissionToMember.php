<?php

namespace module\Assignment\Model\Collection;

/**
 * @ODM\Document(
 *   db="tami_assignment", 
 *   collection="PermissionToMembers"
 * )  
 */
class PermissionToMember extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $member_id;

    /**
     * @ODM\ReferenceOne(targetDocument="module\Assignment\Model\Collection\Permission", inversedBy="members")
     */
    private $permission;

    //member id
    public function getMemberId() {
        return $this->member_id;
    }

    public function setMemberId($member_id) {
        $this->member_id = $member_id;
        return $this;
    }

    //permission
    public function getPermission() {
        return $this->permission;
    }

    public function setPermission(\module\Assignment\Model\Collection\Permission $obj) {
        $this->permission = $obj;
        return $this;
    }

    public function release() {
        $obj = $this->export();

        return $obj;
    }

}
