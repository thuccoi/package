<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="MemberLogs", db="tami_account")
 * @ODM\HasLifecycleCallbacks
 */
class MemberLog extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $app_id;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $user_id;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $metatype;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $message;

    public function __construct($app_id = "", $user_id = "", $metatype = "", $message = "") {
        $this->init();
        $this->app_id = $app_id;
        $this->user_id = $user_id;

        $this->metatype = $metatype;

        $this->message = $message;
    }

    public function release() {
        $obj = $this->export();
        $obj->app_id = $this->getAppId();
        $obj->user_id = $this->getUserId();
        $obj->type = $this->getType();
        $obj->message = $this->getMessage();

        return $obj;
    }

    //app id
    public function setAppId($app_id) {
        $this->app_id = $app_id;
        return $this;
    }

    public function getAppId() {
        return $this->app_id;
    }

    //user id
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }

    public function getUserId() {
        return $this->user_id;
    }

    //meta type
    public function setMetatype($metatype) {
        $this->metatype = $metatype;
        return $this;
    }

    public function getMetatype() {
        return $this->metatype;
    }

    //message
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    public function getMessage() {
        return $this->message;
    }

}
