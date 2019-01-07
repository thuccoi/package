<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_account", 
 *   collection="UserLogs"
 * )  
 * @ODM\HasLifecycleCallbacks
 */
class UserLog extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldLogDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $user_id;

    public function __construct($user_id, $metatype = "", $message = "") {
        $this->initLog($metatype, $message);
        $this->user_id = $user_id;
    }

    public function release() {
        $obj = $this->exportLog();
        $obj->user_id = $this->getUserId();

        return $obj;
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
