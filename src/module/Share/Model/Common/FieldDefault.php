<?php

namespace module\Share\Model\Common;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait FieldDefault {

    /**
     *
     * @ODM\Id
     */
    private $id;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $app_id;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $creator_id;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $token;

    /**
     *
     * @ODM\Field(type="date")
     */
    private $create_at;

    /**
     *
     * @ODM\Field(type="date")
     */
    private $update_at;

    /**
     *
     * @ODM\Field(type="hash")
     */
    private $options;

    /**
     * 
     * @ODM\Field(type="date")
     */
    private $deleted_at;

    public function getDeletedAt() {
        if ($this->deleted_at) {

            return \system\Helper\Str::toTimeString($this->deleted_at);
        }

        return '';
    }

    public function delete() {
        $this->deleted_at = new \DateTime();
        return $this;
    }

    public function restore() {
        $this->deleted_at = null;
        return $this;
    }

    public function __construct() {
        $this->init();
    }

    //function construct
    public function init() {
        $this->id = new \MongoId();

        //generate token
        $this->token = \system\Helper\Str::rand();

        $this->create_at = new \DateTime();
        $this->update_at = new \DateTime();
    }

    //function set and get
    public function getId() {
        return $this->id;
    }

    public function setId(\MongoId $id) {
        $this->id = $id;
        return $this;
    }

    //app id
    public function getAppId() {
        return $this->app_id;
    }

    public function setAppId($app_id) {
        $this->app_id = $app_id;
        return $this;
    }

    //creator id
    public function getCreatorId() {
        return $this->creator_id;
    }

    public function setCreatorId($creator_id) {
        $this->creator_id = $creator_id;
        return $this;
    }

    //get token
    public function getToken() {
        return $this->token;
    }

    public function getCreateAt() {
        if ($this->create_at) {
            return \system\Helper\Str::toTimeString($this->create_at);
        }

        return '';
    }

    public function setCreateAt(\DateTime $create_at) {
        $this->create_at = $create_at;
        return $this;
    }

    public function getUpdateAt() {
        if ($this->update_at) {
            return \system\Helper\Str::toTimeString($this->update_at);
        }

        return '';
    }

    public function setUpdateAt(\DateTime $update_at) {
        $this->update_at = $update_at;
        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOptions($options) {
        $this->options = $options;
        return $this;
    }

    //function export
    public function export() {
        return (object) [
                    "id"         => $this->getId(),
                    "app_id"     => $this->getAppId(),
                    "creator_id" => $this->getCreatorId(),
                    "create_at"  => $this->getCreateAt(),
                    "update_at"  => $this->getUpdateAt(),
                    "deleted_at" => $this->getDeletedAt(),
                    "options"    => $this->getOptions()
        ];
    }

}
