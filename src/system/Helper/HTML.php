<?php

namespace system\Helper;

class HTML {

    public static $TAMI_GET = "GET";
    public static $TAMI_POST = "POST";

    public static function addQuery($code, $stringparam) {
        //url decode
        $stringurl = urldecode($stringparam);

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

        //analytis arr
        if (count($arr) == 3) {
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
        $router = new \system\Router($module, $controller, $action);

        return $router;
    }
}
