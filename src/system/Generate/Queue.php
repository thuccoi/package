<?php

namespace system\Generate;

class Queue {

    public static function work($argv) {
        $config = \system\Template\Container::getSysConfig();


        //execute on config of queue
        if (isset($config['queue'])) {

            $filename = $config['DIR_ROOT'] . "/logs/queue.sh";

            $cmd = '#!/bin/sh' . PHP_EOL;

            foreach ($config['queue'] as $val) {
                $cmd = $cmd . 'QUEUE=' . $val['name'] . ' COUNT=' . $val['processes'] . ' vendor/bin/resque & ' . PHP_EOL;
            }

            $cmd = $cmd . ' wait ' . PHP_EOL . 'echo all processes complete';
            
            $handle = fopen($filename, "w");
            if ($handle) {
                fwrite($handle, $cmd);
                fclose($handle);
            }

            Kernel::execute("sh {$filename}  2>&1; echo $?");
        } else {
            Kernel::execute("QUEUE=default COUNT=1 vendor/bin/resque  2>&1; echo $?");
        }
    }

}
