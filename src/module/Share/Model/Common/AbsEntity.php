<?php

namespace module\Share\Model\Common;

abstract class AbsEntity {

    //create new object
    abstract public function create();

    //find object
    abstract public function find();
}
