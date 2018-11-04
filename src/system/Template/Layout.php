<?php

namespace system\Template;

class Layout {

    private $title;
    private $view_file;
    private $layout;
    private $url;
    private $view_dir;
    private $param = [];
    private $config;
    //viewer
    private $viewer;

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

    public function getConfig($name = '') {
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

    public function setParam($param) {
        $this->param = $param;
        return $this;
    }

    public function getParam() {
        return $this->param;
    }

    //viewer
    public function setViewer($viewer) {
        $this->viewer = $viewer;
        return $this;
    }

    public function getViewer() {
        return $this->viewer;
    }

    //function show
    public function showLayout(array $param = null) {
        //make parameters
        if ($param) {
            foreach ($param as $key => $val) {
                $$key = $val;
            }
        }

        require_once $this->getLayout();
    }

    public function showViewFile(array $param = null) {
        //make parameters
        if ($param) {
            foreach ($param as $key => $val) {
                $$key = $val;
            }
        }

        //set view param in action
        foreach ($this->param as $key => $val) {
            $$key = $val;
        }

        require_once $this->getViewFile();
    }

    public function title($title) {
        $this->setTitle($title);
        echo "<title>{$this->getTitle()}</title>";

        return $this;
    }

    public function css($href) {
        if (is_array($href)) {
            foreach ($href as $val) {
                echo '<link rel="stylesheet"  type="text/css" href="' . $val . '" />';
            }
        } else {
            echo '<link rel="stylesheet"  type="text/css" href="' . $href . '" />';
        }
        return $this;
    }

    public function js($src) {
        if (is_array($src)) {
            foreach ($src as $val) {
                echo '<script type="text/javascript" src="' . $val . '"></script>';
            }
        } else {
            echo '<script type="text/javascript" src="' . $src . '"></script>';
        }
        return $this;
    }

    public function partial($file, array $param = null) {
        $dir = $this->getViewDir() . $file;


        if (!file_exists($dir)) {
            echo "View file not exists";
            exit;
        }

        //make parameters
        if ($param) {
            foreach ($param as $key => $val) {
                $$key = $val;
            }
        }
        require_once $dir;
    }

    /**
     * 
     * @param type $module
     * @param array $options include controller, action, id, param
     * @return url
     */
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
        $router = new \system\Router($module, $controller, $action, $id, $param, $this->config);

        return $router->url();
    }

}
