<?php

namespace system\Queue;

abstract class AbsJob {

    use TraitDispatch;
    
    public function setUp() {
        // ... Set up environment for this job
    }

    abstract public function perform();// ..Run job

    public function tearDown() {
        // ... Remove environment for this job
    }
}
