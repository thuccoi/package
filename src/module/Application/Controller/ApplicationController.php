<?php

namespace module\Application\Controller;

class ApplicationController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\Application($connect, $code, $config);
    }

    public function indexAction() {
        
    }

    public function createAction() {
        //get data
        $data = (object) [
                    "name" => $this->getCode()->post("name"),
                    "metatype" => $this->getCode()->post("metatype"),
                    "domain" => $this->getCode()->post("domain")
        ];

        //register new an user
        $this->entity->create($data);
    }

    public function updateAction() {
        
    }

    public function removeAction() {
        
    }

}
