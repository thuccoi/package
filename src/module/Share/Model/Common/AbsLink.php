<?php

namespace module\Share\Model\Common;

abstract class AbsLink {

    //add new object
    abstract public function add($data);

    abstract public function update($id, $data);

    abstract public function remove($id);

    //find object
    abstract public function find($id, $type = '');
}
