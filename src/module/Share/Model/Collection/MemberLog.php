<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="MemberLogs", db="tami_account")
 * @ODM\HasLifecycleCallbacks
 */
class MemberLog extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldLogDefault;

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

    public function __construct($app_id = "", $user_id = "", $metatype = "", $message = "") {
        $this->initLog($metatype, $message);
        $this->app_id = $app_id;
        $this->user_id = $user_id;
    }

    public function release() {
        $obj = $this->exportLog();
        $obj->app_id = $this->getAppId();
        $obj->user_id = $this->getUserId();

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

}
