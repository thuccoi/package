<?php

namespace system\Queue;

class Queue {

    protected $job;

    public function __construct($job) {
        $this->job = $job;
    }

    public function onQueue($queue = "default") {

        return $this;
    }

    public function delay($seconds = 0) {

        return $this;
    }

    public function at($timestamp = 0) {

        return $this;
    }

}
