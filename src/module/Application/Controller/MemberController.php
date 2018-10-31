<?php

namespace module\Application\Controller;

class MemberController extends \system\Template\AbstractController {

    //member
    private $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\Member($connect, $code, $config);
    }

    public function createAction() {
        $data = (object) [
                    "application" => $this->getCode()->post("application"),
                    "user" => $this->getCode()->post("user")
        ];
        
        //create new a member
        $this->entity->create($data);
    }

    public function ownerAction() {
        
    }

    public function adminAction() {
        
    }

    public function defaultAction() {
        
    }

}
