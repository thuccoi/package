<?php

namespace system\Queue;

class ExecuteJob {

    use TraitDispatch;

    public function setUp() {
        // ... Set up environment for this job
    }

    public function perform() {
        $job = unserialize($this->args['job']);
        $job->handle();
    }

    public function tearDown() {
        // ... Remove environment for this job
    }

}
