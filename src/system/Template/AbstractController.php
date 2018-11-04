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
                        "app" => $session->get("app"),
                        "user" => $session->get("user"),
                        "member" => $session->get("member")
            ];
        }
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

    public function getConfig() {
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

}
