<?php

namespace system\Queue;

class Queue {

    protected $job;
    public $queue;

    public function __construct($job) {
        $this->job = $job;
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
        \Resque::enqueue($this->queue, \system\Queue\ExecuteJob::class, ['job' => serialize($this->job)], true);
    }

}
