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
     * @ODM\ReferenceOne(targetDocument="module\Share\Model\Collection\Member", inversedBy="roles")
     */
    private $member;

    //role
    public function getRole() {
        return $this->role;
    }

    public function setRole(\module\Assignment\Model\Collection\Role $obj) {
        $this->role = $obj;
        return $this;
    }

    //member
    public function getMember() {
        return $this->member;
    }

    public function setMember(\module\Share\Model\Collection\Member $member) {
        $this->member = $member;
        return $this;
    }

    public function release() {
        $obj = $this->export();

        return $obj;
    }

}
