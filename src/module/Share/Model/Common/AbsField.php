<?php

namespace module\Share\Model\Common;

abstract class AbsField {

    //config system not save, load insystem
    private $tami_config;

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

    //tami config
    public function setTamiConfig($config) {
        $this->tami_config = $config;
        return $this;
    }

    public function getTamiConfig() {
        return $this->tami_config;
    }

}
