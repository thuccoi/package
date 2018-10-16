<?php

namespace system\Template;

abstract class AbstractController {

    private $router;
    private $code;
    private $config;
    private $options;

    private $layout;
    
    //init from factory
    public function __construct(\system\Router $router, \system\Helper\Code $code, array $config = null, array $options = null) {
        $this->router = $router;
        //set router default
        $this->router->setConfigDefault($config['routerDefault']);
        
        $this->code = $code;
        $this->config = $config;
        $this->options = $options;
    }

    //function get
    public function getRouter() {
        return $this->router;
    }

    public function getCode() {
        return $this->code;
    }

    public function getConfig() {
        return $this->config;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getLayout() {
        return $this->layout;
    }

    public function setLayout($layout) {
        
         $this->layout = $layout;
    }

}
