<?php

namespace system\Helper;

class HTML {

    public static $TAMI_GET = "GET";
    public static $TAMI_POST = "POST";

    public static function addQuery($code, $stringparam) {
        //url decode
        $stringurl = urldecode($stringparam);

        if ($stringurl) {

            //get document
            $documents = explode('&', $stringurl);

            if ($documents) {
                foreach ($documents as $val) {
                    $document = explode('=', $val);
                    if (count($document) == 2) {
                        $_GET[$code->purify($document[0])] = $code->purify($document[1]);
                    }
                }
            }
        }
    }

    public static function getPathUri($code, $path_uri, $sysconfig) {
        //check config router
        if (!isset($sysconfig['routerDefault'])) {
            echo "Router default not config";
            exit;
        }

        if (!isset($sysconfig['routerDefault']['module'])) {
            echo "Router default module not config";
            exit;
        }


        if (!isset($sysconfig['routerDefault']['controller'])) {
            echo "Router default controller not config";
            exit;
        }


        if (!isset($sysconfig['routerDefault']['action'])) {
            echo "Router default action not config";
            exit;
        }



        $detach = explode("/", $path_uri);

        //purify detach
        $arr = [];
        if ($detach) {
            foreach ($detach as $val) {
                if ($val) {
                    $arr [] = $code->purify($val);
                }
            }
        }

        //output
        $module = "";
        $controller = "";
        $action = "";
        $id = "";

        //analytis arr
        if (count($arr) == 4) {
            $module = $arr[0];
            $controller = $arr[1];
            $action = $arr[2];
            $id = $arr[3];
        } else if (count($arr) == 3) {
            $module = $arr[0];
            $controller = $arr[1];
            $action = $arr[2];
        } else if (count($arr) == 2) {
            $module = $arr[0];
            $controller = $arr[1];
            $action = $sysconfig['routerDefault']['action'];
        } else if (count($arr) == 1) {
            $module = $arr[0];
            $controller = $sysconfig['routerDefault']['controller'];
            $action = $sysconfig['routerDefault']['action'];
        } else if (count($arr) == 0) {
            $module = $sysconfig['routerDefault']['module'];
            $controller = $sysconfig['routerDefault']['controller'];
            $action = $sysconfig['routerDefault']['action'];
        } else {
            echo "Invalid URL";
            exit;
        }


        //init router
        $router = new \system\Router($module, $controller, $action, $id, [], $sysconfig);

        return $router;
    }

    /**
     * 
     *   $files = array(
     *       'http://myframework.com/static/plugins/core/pace/pace.min.js',
     *       'http://myframework.com/static/js/libs/jquery-2.1.1.min.js',
     *       'http://myframework.com/static/js/libs/jquery-ui-1.10.4.min.js',
     *   );
     *
     *  echo \system\Helper\HTML::combine($files, 'minified_files/', md5("my_mini_file") . ".js");
     */
    public static function combine($array_files, $destination_dir, $dest_file_name) {

        if (!is_file($destination_dir . $dest_file_name)) { //continue only if file doesn't exist
            $content = "";
            foreach ($array_files as $file) { //loop through array list
                $content .= file_get_contents($file); //read each file
            }

            //You can use some sort of minifier here 
            //minify_my_js($content);

            $new_file = fopen($destination_dir . $dest_file_name, "w"); //open file for writing
            fwrite($new_file, $content); //write to destination
            fclose($new_file);

            return '<script src="' . $destination_dir . $dest_file_name . '"></script>'; //output combined file
        } else {
            //use stored file
            return '<script src="' . $destination_dir . $dest_file_name . '"></script>'; //output combine file
        }
    }

}
