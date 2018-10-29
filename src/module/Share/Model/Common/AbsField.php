<?php

namespace module\Share\Model\Common;

abstract class AbsField {

    use FieldDefault;

    public function __construct($config) {
        $this->id = new \MongoId();
        $this->create_at = new \DateTime();
        $this->update_at = new \DateTime();
        $this->options = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tami_config = $config;
    }

    //release function
    abstract public function release();
}
