<?php

namespace system\Template;

class Layout {

    private $title;
    private $view_file;
    private $layout;
    private $url;
    private $view_dir;
    private $params = [];
    private $config;

    //function set and get
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setViewFile($view_file) {
        $this->view_file = $view_file;
        return $this;
    }

    public function getViewFile() {
        return $this->view_file;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    public function getViewDir() {
        return $this->view_dir;
    }

    public function setViewDir($view_dir) {
        $this->view_dir = $view_dir;
        return $this;
    }

    public function getLayout() {
        return $this->layout;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setConfig($config) {
        $this->config = $config;
        return $this;
    }

    public function getConfig() {
        return $this->config;
    }

    public function setParams($params) {
        $this->params = $params;
        return $this;
    }

    public function getParams() {
        return $this->params;
    }

    //function show
    public function showLayout(array $params = null) {
        //make parameters
        if ($params) {
            foreach ($params as $key => $val) {
                $$key = $val;
            }
        }

        require_once $this->getLayout();
    }

    public function showViewFile(array $params = null) {
        //make parameters
        if ($params) {
            foreach ($params as $key => $val) {
                $$key = $val;
            }
        }

        //set view params in action
        foreach ($this->params as $key => $val) {
            $$key = $val;
        }

        require_once $this->getViewFile();
    }

    public function title($title) {
        $this->setTitle($title);
        echo "<title>{$this->getTitle()}</title>";
    }

    public function partial($file, array $params = null) {
        $dir = $this->getViewDir() . $file;


        if (!file_exists($dir)) {
            echo "View file not exists";
            exit;
        }

        //make parameters
        if ($params) {
            foreach ($params as $key => $val) {
                $$key = $val;
            }
        }
        require_once $dir;
    }

    public function url($module, array $options = null) {
        
        //default router
        $controller = $this->config['routerDefault']['controller'];
        $action = $this->config['routerDefault']['action'];

        //controller
        if (isset($options['controller'])) {
            $controller = $options['controller'];
        }

        //action
        if (isset($options['action'])) {
            $action = $options['action'];
        }

        //id
        $id = '';
        if (isset($options['id'])) {
            $id = $options['id'];
        }

        //param
        $param = [];
        if (isset($options['param'])) {
            $param = $options['param'];
        }

        //make router
        $router = new \system\Router($module, $controller, $action, $id, $param);

        return $router->makeURL();
    }

}
