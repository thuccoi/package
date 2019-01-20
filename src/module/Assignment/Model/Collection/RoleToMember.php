<?php

namespace module\Assignment\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_assignment", 
 *   collection="RoleToMembers"
 * )  
 */
class RoleToMember extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldDefault;

    /**
     * @ODM\ReferenceOne(targetDocument="module\Assignment\Model\Collection\Role", inversedBy="members")
     */
    private $role;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $member_id;

    //role
    public function getRole() {
        return $this->role;
    }

    public function setRole(\module\Assignment\Model\Collection\Role $obj) {
        $this->role = $obj;
        return $this;
    }

    //member id
    public function getMemberId() {
        return $this->member_id;
    }

    public function setMemberId($member_id) {
        $this->member_id = $member_id;
        return $this;
    }

    public function release() {
        $obj = $this->export();

        return $obj;
    }

}
