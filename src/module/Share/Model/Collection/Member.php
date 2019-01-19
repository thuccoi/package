<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Members", db="tami_account")
 */
class Member extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldDefault;

    /**
     *
     * @ODM\ReferenceOne(targetDocument=module\Share\Model\Collection\App::class, inversedBy="members")
     */
    private $app;

    /**
     *
     * @ODM\ReferenceOne(targetDocument=module\Share\Model\Collection\User::class, inversedBy="members")
     */
    private $user;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $role;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $title;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $alias;

    /**
     *
     * @ODM\Field(type="int")
     */
    private $status;

    //role
    const ROLE_OWNER = "owner";
    const ROLE_ADMIN = "admin";
    const ROLE_DEFAULT = "member";
    //status
    const STATUS_ACTIVATE = 1;
    const STATUS_DEACTIVE = -1;
    const STATUS_PENDING = 0;

    public function __construct(\module\Share\Model\Collection\App $app, \module\Share\Model\Collection\User $user) {
        $this->init();
        $this->app = $app;
        $this->user = $user;


        //role default default
        $this->role = self::ROLE_DEFAULT;


        //activate status
        $this->status = self::STATUS_ACTIVATE;
    }

    //set viewer session
    public function setViewer($session) {
        //set session
        $session->set("auth", 1);
        $session->set("role", $this->getRole());
        $session->set("app", $this->getApp()->release());
        $session->set("user", $this->getUser()->release());
        $session->set("member", $this->release());

        return $this;
    }

    //title
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    //alias
    public function setAlias($alias) {
        $this->alias = $alias;
        return $this;
    }

    public function getAlias() {
        return $this->alias;
    }

    //add app
    public function setApp(\module\Share\Model\Collection\App $app) {
        $this->app = $app;
        return $this;
    }

    public function getApp() {
        return $this->app;
    }

    //add user
    public function setUser(\module\Share\Model\Collection\User $user) {
        $this->user = $user;
        return $this;
    }

    public function getUser() {
        return $this->user;
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
        //role is admin 
        return ($this->role == self::ROLE_ADMIN);
    }

    public function isDefault() {
        //role is default
        return ($this->role == self::ROLE_DEFAULT);
    }

    public function release() {
        $obj = $this->export();
        $obj->role = $this->getRole();
        $obj->title = $this->getTitle();
        $obj->alias = $this->getAlias();
        $obj->status = $this->getStatus();
        $obj->token = $this->getToken();
        return $obj;
    }

    //status
    public function getStatus() {
        return $this->status;
    }

    //check status
    public function isActivate() {
        return ($this->status == self::STATUS_ACTIVATE);
    }

    public function isDeactivate() {
        return ($this->status == self::STATUS_DEACTIVE);
    }

    //active account
    public function activate() {
        $this->status = self::STATUS_ACTIVATE;

        return $this;
    }

    //deactive account
    public function deactivate() {
        $this->status = self::STATUS_DEACTIVE;

        return $this;
    }

}
