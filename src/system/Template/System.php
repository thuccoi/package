<?php

namespace system\Template;

class System {

    public static function run($init) {
        if ($init) {
            if (isset($init["view_file"]) && isset($init['layout'])) {

                $layout = new \system\Template\Layout();
                
                $layout->setConfig($init['sysconfig']);
                
                $layout->setLayout($init['layout']);
                $layout->setViewFile($init['view_file']);


                //init parameters
                if (isset($init['parameters']) && $init['parameters']) {
                    $layout->setParams($init['parameters']);
                }



                if (!file_exists($init["view_file"])) {
                    echo "View file: {$init["view_file"]} not exists";
                    exit;
                }

                //set view dir
                $layout->setViewDir($init['view_dir']);


                //check no layout
                if ($init['layout'] != 'TAMI_NOLAYOUT') {

                    //check file layout exists
                    if (!file_exists($init["layout"])) {
                        echo "Layout: {$init["layout"]} not exists";
                        exit;
                    }

                    $layout->showLayout();
                } else {
                    $layout->showViewFile();
                }
            } else {
                echo "Not exists view file";
                exit;
            }
        } else {
            echo "system not initialize";
            exit;
        }
    }

    public static function init() {

        //get config of system
        $sysconfig = \system\Template\Container::getSysConfig();

        $code = new \system\Helper\Code();

        $request_uri = $_SERVER['REQUEST_URI'];


        //get path and params ..
        $path = "/";
        $arrrequest = explode('?', $request_uri);

        if (isset($arrrequest[0])) {
            $path = $arrrequest[0];
        }

        //add data to $_GET from url
        if (isset($arrrequest[1])) {
            \system\Helper\HTML::addQuery($code, $arrrequest[1]);
        }

        $router = \system\Helper\HTML::getPathUri($code, $path, $sysconfig);

        $module = $router->getModule();

        $controller = $router->getController();

        $action = $router->getAction();


        $config = self::getModuleConfig($module, $controller);

        if ($config) {
            if (isset($config['controller'])) {

                if (!isset($config['factory'])) {
                    echo "Not found factory in module config";
                    exit;
                }

                $factory = $config['factory'];

                $objfactory = new $factory;

                //init controller
                $obj = $objfactory($config['controller'], $router, $code, $sysconfig, []);

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
                    echo "Method " . $naction . "Action(){...} not exists in {$config['controller']}";
                    exit;
                }

                //get parameters 
                $parameters = $obj->{$naction . "Action"}();

                //set layout
                $layout = $config["layout"];
                if ($obj->getLayout()) {
                    if ($obj->getLayout() != 'TAMI_NOLAYOUT') {
                        $layout = $config['view_dir'] . $obj->getLayout();
                    } else {
                        $layout = $obj->getLayout();
                    }
                }

                return [
                    "parameters" => $parameters,
                    "view_file" => $config['view_dir'] . $controller . '/' . $action . '.tami',
                    "layout" => $layout,
                    "view_dir" => $config['view_dir'],
                    "sysconfig" => $sysconfig
                ];
            } else {
                echo "Not found controller config";
                exit;
            }
        } else {
            echo "Not get module config";
            exit;
        }
    }

    public static function getModuleConfig($module, $controller) {
        //load config of module
        foreach (TAMI_MODULE as $val) {
            $classname = $val . '\\Module';

            $obj = new $classname();

            //get module config
            $config = $obj->getConfig();

            if (isset($config['router'])) {

                //check view manager
                if (!isset($config['view_manager'])) {
                    echo "Not config view manager";
                    exit;
                }

                if (!isset($config['view_manager']['layout'])) {
                    echo "Not config layout of view manager";
                    exit;
                }


                //accept module config
                if (isset($config['router'][$module])) {

                    //get factory
                    if (!isset($config['controller']) || !$config['controller']) {
                        echo "Not found config controller in module config: {$val}";
                        exit;
                    }

                    if (!isset($config['controller']['factories'])) {
                        echo "Not found config factories in module config: {$val}";
                        exit;
                    }

                    //accept controller
                    if (isset($config['router'][$module][$controller])) {

                        if (!isset($config['controller']['factories'][$config['router'][$module][$controller]])) {
                            echo "{$config['router'][$module][$controller]} has not factory";
                            exit;
                        }

                        //factory of controller
                        $factory = $config['controller']['factories'][$config['router'][$module][$controller]];

                        return [
                            "controller" => $config['router'][$module][$controller],
                            "factory" => $factory,
                            "view_dir" => DIR_ROOT . 'module/' . $val . '/view/',
                            "layout" => $config['view_manager']['layout'],
                        ];
                    } else {
                        echo 'Controller Not found';
                        exit;
                    }
                }
            } else {
                echo "Module not config router";
                exit;
            }
        }
    }

}
