<?php

namespace system\Template;

class System {

    public static function run($init) {
        if ($init) {
            if (isset($init["view_file"]) && isset($init['layout'])) {

                $layout = new \system\Template\Layout($init['sysconfig']);

                $layout->setLayout($init['layout']);
                $layout->setViewFile($init['view_file']);


                //init parameters
                if (isset($init['parameters']) && $init['parameters']) {
                    $layout->setParam($init['parameters']);
                }

                //init param js
                if (isset($init['paramjs']) && $init['paramjs']) {
                    $layout->setParamJs($init['paramjs']);
                }




                if (!file_exists($init["view_file"])) {
                    $init["code"]->notfound("View file: {$init["view_file"]} not exists");
                }

                //set view dir
                $layout->setViewDir($init['view_dir']);

                //set code
                $layout->setCode($init["code"]);

                //set viewer
                $layout->setViewer($init['viewer']);

                //check no layout
                if ($init['layout'] != 'TAMI_NOLAYOUT') {

                    //check file layout exists
                    if (!file_exists($init["layout"])) {
                        $init["code"]->notfound("Layout: {$init["layout"]} not exists");
                    }

                    $layout->showLayout();
                } else {
                    $layout->showViewFile();
                }
            } else {
                $init["code"]->notfound("Not exists view file");
            }
        } else {
            $init["code"]->notfound("system not initialize");
        }
    }

    public static function init($loader) {

        //get config of system
        $sysconfig = \system\Template\Container::getSysConfig();

        //make connect
        $connect = \system\Database\DoctrineMongo::connect($loader, $sysconfig);
        $code = new \system\Helper\Code($sysconfig, $connect);

        $request_uri = $_SERVER['REQUEST_URI'];


        //get path and param ..
        $path = "/";
        $arrrequest = explode('?', $request_uri);

        if (isset($arrrequest[0])) {
            $path = $arrrequest[0];
        }


        //add data to $_GET from url
        if (isset($arrrequest[1])) {
            \system\Helper\HTML::addQuery($code, $arrrequest[1]);
        }

        //get router
        $router = \system\Helper\HTML::getPathUri($code, $path, $sysconfig);

        $module = $router->getModule();

        $controller = $router->getController();

        $action = $router->getAction();

        //new session
        $session = new \system\Session($sysconfig);

        //session working
        $session->working();

        //config of module
        $config = self::getModuleConfig($module, $controller, $session, $sysconfig, $code);

        if ($config) {
            if (isset($config['controller'])) {

                if (!isset($config['factory'])) {
                    $code->notfound("Not found factory in module config");
                }

                $factory = $config['factory'];

                $objfactory = new $factory;

                //init controller
                $obj = $objfactory($connect, $config['controller'], $router, $code, $session, $sysconfig, []);

                //checkpermisison
                if (!self::checkOutsideRouter($module, $controller, $action, $sysconfig)) {
                    if (!self::checkPermission($obj->getViewer()->allowed_actions, $module, $controller, $action, $sysconfig)) {
                        $code->forbidden("You don't have permission to access");
                    }
                }


                $naction = "";
                for ($i = 0; $i < strlen($action); $i++) {
                    if ($action[$i] == "-") {
                        $naction = $naction . strtoupper($action[$i + 1]);
                        $i = $i + 1;
                    } else {
                        $naction = $naction . $action[$i];
                    }
                }



                //echo method exists
                if (!method_exists($obj, $naction . "Action")) {
                    $code->notfound("Method " . $naction . "Action(){...} not exists in {$config['controller']}");
                }

                //get parameters 
                $parameters = $obj->{$naction . "Action"}();

                //get paramjs
                $paramjs = $obj->getParamJs();

                //view dir
                $view_dir = $config['view_dir'];

                if ($obj->getViewDir()) {
                    $view_dir = $obj->getViewDir();
                }

                //set layout
                $layout = $config["layout"];
                if ($obj->getLayout()) {
                    if ($obj->getLayout() != 'TAMI_NOLAYOUT') {
                        $layout = $view_dir . $obj->getLayout();
                    } else {
                        $layout = $obj->getLayout();
                    }
                }


                return [
                    "parameters" => $parameters,
                    "paramjs"    => $paramjs,
                    "view_file"  => $view_dir . $controller . '/' . $action . '.tami',
                    "layout"     => $layout,
                    "view_dir"   => $view_dir,
                    "sysconfig"  => $sysconfig,
                    "viewer"     => $obj->getViewer(),
                    "code"       => $obj->getCode()
                ];
            } else {
                $code->notfound("Not found controller config");
            }
        } else {
            $code->notfound("Not get module config");
        }
    }

    public static function getModuleConfig($module, $controller, $session, $sysconfig, $code) {
        //load config of module
        foreach (TAMI_MODULE as $val) {
            $classname = $val . '\\Module';

            $obj = new $classname();

            //get module config
            $config = $obj->getConfig();

            //check oauth
            if (method_exists($obj, "oauth")) {
                if (!$obj->oauth($session)) {
                    //redirect to login
                }
            }

            if (isset($config['router'])) {

                //check view manager
                if (!isset($config['view_manager'])) {
                    $code->notfound("Not config view manager");
                }

                if (!isset($config['view_manager']['layout'])) {
                    $code->notfound("Not config layout of view manager");
                }


                //accept module config
                if (isset($config['router'][$module])) {

                    //get factory
                    if (!isset($config['controller']) || !$config['controller']) {
                        $code->notfound("Not found config controller in module config: {$val}");
                    }

                    if (!isset($config['controller']['factories'])) {
                        $code->notfound("Not found config factories in module config: {$val}");
                    }

                    //accept controller
                    if (isset($config['router'][$module][$controller])) {

                        if (!isset($config['controller']['factories'][$config['router'][$module][$controller]])) {
                            $code->notfound("{$config['router'][$module][$controller]} has not factory");
                        }

                        //factory of controller
                        $factory = $config['controller']['factories'][$config['router'][$module][$controller]];

                        return [
                            "controller" => $config['router'][$module][$controller],
                            "factory"    => $factory,
                            "view_dir"   => DIR_ROOT . 'module/' . $val . '/view/',
                            "layout"     => $config['view_manager']['layout'],
                        ];
                    } else {
                        $code->notfound('Controller Not found');
                    }
                }
            } else {
                $code->notfound("Module not config router");
            }
        }
    }

    //check permission
    public static function checkPermission($allowed_actions, $module, $controller, $action, $config) {


        foreach ($allowed_actions as $val) {


            if ("$module/$controller/$action" == $val) {
                return true;
            }

            if ($val == "*") {
                return true;
            }


            if (strpos($val, "*") !== false) {
                $rr = explode("/", $val);

                switch (count($rr)) {
                    case 2:
                        if ($module == $rr[0]) {
                            return true;
                        }
                        break;
                    case 3:
                        if ($module == $rr[0] && $controller == $rr[1]) {
                            return true;
                        }
                        break;
                }
            }
        }


        return false;
    }

    //check outside router
    public static function checkOutsideRouter($module, $controller, $action, $config) {
        //check outside router
        $outsideRouter = $config['outsideRouter'];
        foreach ($outsideRouter as $val) {
            if (isset($val['module'])) {
                if ($val['module'] == $module) {//module
                    if (isset($val['controller'])) {
                        if ($val['controller'] == $controller) {//controller
                            if (isset($val['action'])) {
                                if ($val['action'] == $action) {//action
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    } else {
                        return true;
                    }
                }
            }
        }

        return false;
    }

}
