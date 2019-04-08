<?php

namespace system\Helper;

class Log {


    public static function info($message) {
        self::writeLog("info", $message);
    }

    public static function error($message) {
        self::writeLog("error", $message);
    }

    public static function warning($message) {
        self::writeLog("warning", $message);
    }

    private static function writeLog($filename, $message) {
        
        $config = \system\Template\Container::getSysConfig();
        $filename = $config['DIR_ROOT']."/logs/{$filename}";
        
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
