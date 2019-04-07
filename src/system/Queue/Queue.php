<?php

namespace system\Queue;

class Queue {

    protected $classJob;
    protected $args;
    protected $queue;

    protected $seconds_delay;

    public function __construct($classJob, $args) {
        $this->classJob = $classJob;
        $this->args = $args;
    }

    public function onQueue($queue = "default") {
        $this->queue = $queue;
        return $this;
    }

    public function delay($seconds = 0) {
        $this->seconds_delay = $seconds;
        return $this;
    }

    public function __destruct() {
        
        //push to job
        Scheduler::enqueueIn($this->seconds_delay, $this->queue, $this->classJob, $this->args);
    }

}
