<?php

namespace system;

class Router {

    private $module;
    private $controller;
    private $action;
    private $id;
    private $options;
    private $fragment;
    //load config default
    private $config_defaut;
    //static
    public static $TAMI_MODULE = "module";
    public static $TAMI_CONTROLLER = "controller";
    public static $TAMI_ACTION = "action";

    /*
     * function init a new router
     */

    public function __construct($module, $controller, $action, $id = "", $options = []) {

        $this->module = $module;
        $this->controller = $controller;
        $this->action = $action;
        $this->id = $id;

        //handle after own options
        $this->setOptions($options);
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

    public function setFragment($fragment) {
        $this->fragment = $fragment;
        return $this;
    }

    public function getFragment() {
        return $this->fragment;
    }

    public function setOptions($options) {
        $this->options = $options;
        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    //setconfig default
    public function setConfigDefault($config) {
        $this->config_defaut = $config;
    }

    //direct url
    public function redirect($module, array $options = null) {

        $controller = $this->config_defaut['controller'];
        $action = $this->config_defaut['action'];
        $id = "";
        $fragment = "";

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

        //set fragment
        if (isset($options['fragment'])) {
            $fragment = $options['fragment'];
        }

        //make location
        $location = "Location: /{$module}/{$controller}/{$action}";

        //make id
        if ($id) {
            $location = $location . "/{$id}";
        }

        //make param
        if ($parameters) {
            $location = $location . "?" . $parameters;
        }

        //make fragment
        if ($fragment) {
            $location = $location . "#" . $fragment;
        }

        //rediect to router
        header("$location");
        exit;
    }

    //make url
    public function makeURL() {
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
        $url = "/{$this->module}/{$this->controller}/{$this->action}";

        //make id
        if ($this->id) {
            $url = $url . "/{$this->id}";
        }

        if ($parameters) {
            $url = $url . "?" . $parameters;
        }

        //make id
        if ($this->fragment) {
            $url = $url . "#{$this->fragment}";
        }

        return $url;
    }

}
