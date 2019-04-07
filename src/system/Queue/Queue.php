<?php

namespace system\Queue;

class Queue {

    protected $classJob;
    protected $args;
    protected $queue;

    public function __construct($classJob, $args) {
        $this->classJob = $classJob;
        $this->args = $args;
    }

    public function onQueue($queue = "default") {
        $this->queue = $queue;
        return $this;
    }

    public function connection() {
       \Resque::setBackend('localhost:6379');
        return $this;
    }

    public function __destruct() {
        $this->connection();
        
        //push to job
        \Resque::enqueue($this->queue, $this->classJob, $this->args, true);
    }

}
