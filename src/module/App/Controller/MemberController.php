<?php

namespace module\App\Controller;

class MemberController extends \system\Template\AbstractController {

    //member
    private $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Link\Member($connect, $code, $config);
    }

    public function addAction() {


        $data = (object) [
                    "app" => $this->getCode()->post("app"),
                    "user" => $this->getCode()->post("user")
        ];

        //add new a member
        $member = $this->entity->add($data);

        $this->getCode()->success("Create new a member is successfuly");
    }


    public function removeAction() {


        //remove
        $this->entity->remove($this->getCode()->post("id"));
    }

    public function restoreAction() {

        //remove
        $this->entity->restore($this->getCode()->post("id"));
    }

}
