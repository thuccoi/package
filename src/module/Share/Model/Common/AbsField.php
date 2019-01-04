<?php

namespace module\Share\Model\Common;

use \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable;

    abstract class AbsField implements SoftDeleteable {

    //release function
    abstract public function release();
}
