<?php

namespace module\App\Controller;

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
                    "app" => $this->getCode()->post("app"),
                    "user" => $this->getCode()->post("user")
        ];

        //create new a member
        $this->entity->create($data);
    }

    public function ownerAction() {
        //assign owner
        $this->entity->assign($this->getCode()->post("id"), \module\Share\Model\Collection\Member::ROLE_OWNER);
    }

    public function adminAction() {

        //assign admin
        $this->entity->assign($this->getCode()->post("id"), \module\Share\Model\Collection\Member::ROLE_ADMIN);
    }

    public function defaultAction() {
        //assign default
        $this->entity->assign($this->getCode()->post("id"), \module\Share\Model\Collection\Member::ROLE_DEFAULT);
    }

}