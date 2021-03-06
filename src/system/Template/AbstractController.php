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


        $this->paramjs = [];

        //viewer
        $this->viewer = null;

        if ($session->get("auth")) {
            $this->viewer = (object) [
                        "auth"            => $session->get("auth"),
                        "role"            => $session->get("role"),
                        "app"             => $session->get("app"),
                        "app_onboarding"  => $session->get("app_onboarding"),
                        "user"            => $session->get("user"),
                        "member"          => $session->get("member"),
                        "allowed_actions" => $session->get("allowed_actions")
            ];

            //make tami code
            $member_id = $this->viewer->member->id;

            $user_id = $this->viewer->user->id;

            $app_id = $this->viewer->app->id;

            $time = date("Y-m-d H:i");

            //struct member_id user_id app_id time  
            $hash = md5("$member_id.$user_id.$app_id.$time");

            //join member_id and hash to __TAMI_CODE
            $__TAMI_CODE = "$member_id.$hash";

            $this->paramjs['__TAMI_CODE'] = $__TAMI_CODE;
        }

        //set viewer
        $this->paramjs['viewer'] = $this->viewer;

        //login
        $this->toLogin();
    }

    //check auth
    public function toLogin() {

        //if orther login
        if (!($this->router->getModule() == 'a' && $this->router->getController() == 'login')) {

            $flag = FALSE;
            foreach ($this->config['outsideRouter'] as $val) {

                if ($flag === TRUE) {
                    break;
                }

                //module
                if (isset($val['module'])) {
                    //action
                    if (isset($val['action'])) {
                        //controller
                        if (isset($val['controller'])) {

                            if ($this->router->getModule() == $val['module'] && $this->router->getController() == $val['controller'] && $this->router->getAction() == $val['action']) {
                                $flag = TRUE;
                                break;
                            }
                        }
                    } else if (isset($val['controller'])) {
                        if ($this->router->getModule() == $val['module'] && $this->router->getController() == $val['controller']) {
                            $flag = TRUE;
                            break;
                        }
                    } else {
                        if ($this->router->getModule() == $val['module']) {
                            $flag = TRUE;
                            break;
                        }
                    }
                }
            }

            if ($flag === FALSE) {
                //redirect to login
                if (!$this->getViewer() || !$this->getViewer()->auth) {
                    //login
                    $this->router->redirect('a', ['controller' => 'login']);
                }
            }
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

    /**
     * 
     * @param type $module
     * @param array $options include controller, action, id, param
     * @return url
     */
    public function url($module, array $options = null) {

        //get url in layout
        $layout = new \system\Template\Layout($this->config);

        return $layout->url($module, $options);
    }

    /**
     * 
     * @param type $module
     * @param array $options include controller, action, id, param
     * @return url
     */
    public function urlInside($module, array $options = null) {

        //get url in layout
        $layout = new \system\Template\Layout($this->config);

        return $layout->urlInside($module, $options);
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
