<?php

namespace module\Share\Model\Common;

abstract class AbsEntity {

    //create new object
    abstract public function create($data);

    abstract public function edit($id, $data);

    abstract public function delete($id);

    abstract public function restore($id);

    //find object
    abstract public function find($id, $type = '');
}
