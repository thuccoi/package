<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Members", db="tami_account")
 * @ODM\HasLifecycleCallbacks
 */
class Member extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldDefault;

    /**
     *
     * @ODM\ReferenceOne(targetDocument=module\Share\Model\Collection\Application::class, inversedBy="members")
     */
    private $application;

    /**
     *
     * @ODM\ReferenceOne(targetDocument=module\Shate\Model\Collection\User::class, inveredBy="members")
     */
    private $user;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $role;

    //role
    const ROLE_OWNER = "owner";
    const ROLE_ADMIN = "admin";
    const ROLE_DEFAULT = "default";

    public function __construct(\module\Share\Model\Collection\Application $application, \module\Share\Model\Collection\User $user) {
        $this->init();
        $this->application = $application;
        $this->user = $user;


        //role default default
        $this->role = self::ROLE_DEFAULT;
    }

    //add application
    public function setApplication(\module\Share\Model\Collection\Application $application) {
        $this->application = $application;
        return $this;
    }

    //add user
    public function setUser(\module\Share\Model\Collection\User $user) {
        $this->user = $user;
        return $this;
    }

    //role
    public function getRole() {
        return $this->role;
    }

    //assign role
    public function assignOwner() {
        $this->role = self::ROLE_OWNER;
        return $this;
    }

    public function assignAdmin() {
        $this->role = self::ROLE_ADMIN;
        return $this;
    }

    public function assignDefault() {
        $this->role = self::ROLE_DEFAULT;
        return $this;
    }

    //check role
    public function isOwner() {
        //role is owner
        return ($this->role == self::ROLE_OWNER);
    }

    public function isAdmin() {
        //role is admin or owner
        return ($this->isOwner() || $this->role == self::ROLE_ADMIN);
    }

    public function isDefault() {
        //role is default
        return ($this->role == self::ROLE_DEFAULT);
    }

    public function release() {
        $obj = $this->export();
        $obj->role = $this->getRole();
        return $obj;
    }

}
