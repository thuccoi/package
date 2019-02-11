<?php

namespace system\Template;

class Container {

    /**
     * get config include php, ini and json
     * @return config of system
     */
    public static function getSysConfig() {
        //get config
        $conini = self::getSysIni();
        $conjon = self::getSysJson();
        $conphp = self::getSysPhp();

        //merge all config
        $result = [];
        $result = array_merge($conini, $result);
        $result = array_merge($conjon, $result);
        $result = array_merge($conphp, $result);

        return $result;
    }

    /**
     * from config system
     * @return config ini
     */
    public static function getSysIni() {
        //get all file in folder config ini
        $dirini = DIR_ROOT . '/config/ini/autoload/';
        $files = scandir($dirini);

        $result = [];
        if ($files) {
            foreach ($files as $fileini) {
                //get conten in file ini
                if (pathinfo($dirini . $fileini, PATHINFO_EXTENSION) == "ini") {
                    //merge config
                    $result = array_merge(parse_ini_file($dirini . $fileini, true), $result);
                }
            }
        }

        return $result;
    }

    /**
     * from config system
     * @return config json
     */
    public static function getSysJson() {
        //get all file in folder config json
        $dirjson = DIR_ROOT . '/config/json/autoload/';
        $files = scandir($dirjson);

        $result = [];
        if ($files) {
            foreach ($files as $file) {
                //get content in file json
                if (pathinfo($dirjson . $file, PATHINFO_EXTENSION) == "json") {
                    //merge config
                    $result = array_merge(json_decode(file_get_contents($dirjson . $file), true), $result);
                }
            }
        }

        return $result;
    }

    /**
     * from config system
     * @return config php
     */
    public static function getSysPhp() {
        //get all file in folder config php
        $dirphp = DIR_ROOT . '/config/php/autoload/';
        $files = scandir($dirphp);

        $result = [];
        if ($files) {
            foreach ($files as $file) {
                //get content in file php
                if (pathinfo($dirphp . $file, PATHINFO_EXTENSION) == "php") {

                    //merge config
                    $result = array_merge(include $dirphp . $file, $result);
                }
            }
        }

        return $result;
    }

}
