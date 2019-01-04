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

    /**  @ODM\Field(type="date") */
    private $deletedAt;

    public function getDeletedAt() {
        return $this->deletedAt;
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

    //get token
    public function getToken() {
        return $this->token;
    }

    public function getCreateAt() {
        if ($this->create_at) {
            return $this->create_at->format("d/m/Y");
        }

        return '';
    }

    public function setCreateAt(\DateTime $create_at) {
        $this->create_at = $create_at;
        return $this;
    }

    public function getUpdateAt() {
        if ($this->update_at) {
            return $this->update_at->format("d/m/Y");
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
                    "id" => $this->getId(),
                    "create_at" => $this->getCreateAt(),
                    "update_at" => $this->getUpdateAt(),
                    "options" => $this->getOptions()
        ];
    }

}
