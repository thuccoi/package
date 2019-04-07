<?php

namespace system\Helper;

class Log {

    private $config;
    
    public function __construct($config) {
        $this->config = $config;
    }

    public function info($message) {
        $filename = $this->config['DIR_ROOT']. "/logs/info";
        $this->writeLog($filename, $message);
    }

    public function error($message) {
        $filename = $this->config['DIR_ROOT']."/logs/error";
        $this->writeLog($filename, $message);
    }

    public function warning($message) {
        $filename = $this->config['DIR_ROOT']."/logs/warning";
        $this->writeLog($filename, $message);
    }

    private function writeLog($filename, $message) {
        //follow date
        $date = date("d-m-Y");
        $handle = fopen($filename . "_" . $date . ".log", "a+");

        //add message
        if ($handle) {
            $message = "LOG AT " . date("H:i:s") . " MESSAGE IS: " . $message;
            fwrite($handle, $message . PHP_EOL);
        }

        fclose($handle);
    }

}
