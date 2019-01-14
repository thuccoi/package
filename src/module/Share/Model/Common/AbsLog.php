<?php

namespace module\Share\Model\Common;

abstract class AbsLog {

    public static $dm;
    public static $code;
    public static $config;
    public static $parents = [];

    abstract public function add($data);

    final public function log($data) {
        //this
        if (!isset($data->chilren) || $data->chilren == get_class($this)) {
            $data->chilren = "";
        }

        //this
        $this->add($data);

        //list parents
        $parents = static::$parents;
        foreach ($parents as $val) {

            $obj = new $val(static::$dm, static::$code, static::$config);

            $data->chilren = get_class($this);

            $obj->log($data);
        }
    }

}
