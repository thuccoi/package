<?php

namespace module\Share\Model\Common;

abstract class AbsEntity {

    //create new object
    abstract public function create($data);

    //find object
    abstract public function find($id, $type = '');
}
