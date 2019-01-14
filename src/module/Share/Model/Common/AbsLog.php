<?php

namespace module\Share\Model\Common;

abstract class AbsLog {

    public static $parents = [];

    abstract public function add(&$data);

    final public function log(&$data) {
        //this
        if (!isset($data["chilren"]) || $data["children"] == get_class($this)) {
            $data["chilren"] = "";
        }

        //this
        $this->add($data);


        //list parents
        $parents = static::$parents;
        foreach ($parents as $val) {
            $obj = new $val();

            $data["chilren"] = get_class($this);

            $obj->log($data);
        }
    }

}
