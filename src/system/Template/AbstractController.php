<?php

namespace system\Template;

abstract class AbstractController {

    private $connect;
    private $router;
    private $code;
    private $config;
    private $options;
    private $layout;
    private $session;
    //viewer
    private $viewer;
    //view dir
    private $view_dir;
    //parameter js
    private $paramjs;

    //init from factory
    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {

        $this->connect = $connect;

        $this->router = $router;

        $this->code = $code;

        $this->session = $session;

        $this->config = $config;

        $this->options = $options;

        //viewer
        $this->viewer = null;

        if ($session->get("auth")) {
            $this->viewer = (object) [
                        "auth" => $session->get("auth"),
                        "app" => $session->get("app"),
                        "user" => $session->get("user"),
                        "member" => $session->get("member")
            ];
        }

        $this->paramjs = [];
    }

    //function get
    public function getConnect() {
        return $this->connect;
    }

    public function getRouter() {
        return $this->router;
    }

    public function getCode() {
        return $this->code;
    }

    public function getSession() {
        return $this->session;
    }

    public function getConfig($name = "") {
        if ($name) {
            //check exists property $name
            if (isset($this->config[$name])) {
                return $this->config[$name];
            } else {
                return null;
            }
        }
        return $this->config;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getViewer() {
        return $this->viewer;
    }

    //function set
    public function setLayout($layout) {

        $this->layout = $layout;
    }

    public function getLayout() {
        return $this->layout;
    }

    //view directory
    public function setViewDir($view_dir) {

        $this->view_dir = $view_dir;
    }

    public function getViewDir() {
        return $this->view_dir;
    }

    //render parameter in php to javascript
    public function toParamJs($name, $data) {
        $this->paramjs[$name] = $data;
        return $this;
    }

    public function setParamJs($paramjs) {
        $this->paramjs = $paramjs;
        return $this;
    }

    public function getParamJs() {
        return $this->paramjs;
    }

}
