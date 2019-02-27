<?php

namespace module\Share\Model\Common;

trait EntityDefault {

    private $code;
    private $dm;
    private $config;
    private $session;

    //set properties code
    public function __construct($connect, \system\Helper\Code $code, $config, \system\Session $session) {

        // $dm is a DocumentManager instance we should already have
        $this->init($connect, $code, $config, $session);
    }

    public function init($connect, \system\Helper\Code $code, $config, \system\Session $session) {
        $this->code = $code;
        $this->dm = $connect;
        $this->config = $config;
        $this->session = $session;
    }

    //config
    public function setConfig($config) {
        $this->config = $config;
        return $this;
    }

    public function getConfig() {
        return $this->config;
    }

    //code
    public function setCode(\system\Helper\Code $code) {
        $this->code = $code;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    //connect
    public function setConnect($connect) {
        $this->dm = $connect;
        return $this;
    }

    public function getConnect() {
        return $this->dm;
    }

    //session
    public function setSession($session) {
        $this->session = $session;
        return $this;
    }

    public function getSession() {
        return $this->session;
    }

    public function inputTest(&$obj, $data) {
        //token
        if (!\system\Helper\Validate::isEmpty($data->token)) {
            $obj->setToken($data->token);
        }

        //id
        if (!\system\Helper\Validate::isEmpty($data->id)) {
            $obj->setId($data->id);
        }

        //create_at
        if (!\system\Helper\Validate::isEmpty($data->create_at)) {
            $obj->setCreateAt($data->create_at);
        }

        //update_at
        if (!\system\Helper\Validate::isEmpty($data->update_at)) {
            $obj->setUpdateAt($data->update_at);
        }
    }

    public function inputLocal(&$obj) {
        //app
        $app = $this->session->get('app');
        if (!$app || \system\Helper\Validate::isEmpty($app->id)) {
            return false;
        }

        $obj->setAppId($app->id);

        //member
        $member = $this->session->get('member');
        if (!$member || \system\Helper\Validate::isEmpty($member->id)) {
            return false;
        }

        $obj->setCreatorId($member->id);

        return true;
    }

}
