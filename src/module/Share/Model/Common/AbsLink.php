<?php

namespace module\Share\Model\Common;

abstract class AbsLink {

    //add new object
    abstract public function add($data);

    //find object
    abstract public function find($id, $type = '');
}
