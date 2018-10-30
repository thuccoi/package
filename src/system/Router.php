<?php

namespace system;

class Router {

    private $module;
    private $controller;
    private $action;
    private $id;
    private $options;
    //load config default
    private $config;
    //static
    public static $TAMI_MODULE = "module";
    public static $TAMI_CONTROLLER = "controller";
    public static $TAMI_ACTION = "action";

    /*
     * function init a new router
     */

    public function __construct($module, $controller, $action, $id = "", $options = [], array $config = null) {

        $this->module = $module;
        $this->controller = $controller;
        $this->action = $action;
        $this->id = $id;

        //handle after own options
        $this->setOptions($options);
        //set config
        $this->setConfig($config);
    }

    /*
     * the function set and get
     */

    public function setModule($module) {
        $this->module = $module;
        return $this;
    }

    public function getModule() {
        return $this->module;
    }

    public function setController($controller) {
        $this->controller = $controller;
        return $this;
    }

    public function getController() {
        return $this->controller;
    }

    public function setAction($action) {
        $this->action = $action;
        return $this;
    }

    public function getAction() {
        return $this->action;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setOptions($options) {
        $this->options = $options;
        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    //setconfig default
    public function setConfig($config) {
        $this->config = $config;
    }

    //direct url
    public function redirect($module, array $options = null) {

        $controller = $this->config['routerDefault']['controller'];
        $action = $this->config['routerDefault']['action'];
        $id = "";


        //make parameters
        $parameters = '';
        if (isset($options['param'])) {
            $documents = [];

            foreach ($options['param'] as $key => $val) {
                $documents [] = "$key=$val";
            }

            $parameters = implode('&', $documents);
        }

        //set controller
        if (isset($options['controller'])) {
            $controller = $options['controller'];
        }

        //set action
        if (isset($options['action'])) {
            $action = $options['action'];
        }

        //set id
        if (isset($options['id'])) {
            $id = $options['id'];
        }

        //make location
        $location = "Location: {$this->config["URL_ROOT"]}/{$module}/{$controller}/{$action}";

        //make id
        if ($id) {
            $location = $location . "/{$id}";
        }

        //make param
        if ($parameters) {
            $location = $location . "?" . $parameters;
        }

        //rediect to router
        header("$location");
        exit;
    }

    //make url
    public function url() {
        //make parameters
        $parameters = [];
        if ($this->options) {
            $documents = [];
            foreach ($this->options as $key => $val) {

                $documents[] = "$key=$val";
            }

            $parameters = implode("&", $documents);
        }

        //return url
        $url = $this->config["URL_ROOT"] . "/{$this->module}/{$this->controller}/{$this->action}";

        //make id
        if ($this->id) {
            $url = $url . "/{$this->id}";
        }

        if ($parameters) {
            $url = $url . "?" . $parameters;
        }

        return $url;
    }

}
