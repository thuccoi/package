<?php

namespace module\Share\Model\Common;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait FieldLogDefault {

    use \module\Share\Model\Common\FieldDefault;

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

    //function construct
    public function initLog($metatype = "", $message = "") {
        $this->init();

        $this->metatype = $metatype;
        $this->message = $message;
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

    //function export log
    public function exportLog() {
        $obj = $this->export();
        $obj->metatype = $this->getMetatype();
        $obj->message = $this->getMessage();

        return $obj;
    }

}
