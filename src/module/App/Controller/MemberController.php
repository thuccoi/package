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

        //assign
        $role = $this->getCode()->post("role");
        if (!\system\Helper\Validate::isEmpty($role)) {
            //check role default
            if ($role != \module\Share\Model\Collection\Member::ROLE_DEFAULT) {
                $this->entity->assign($member->getId(), $role);
            }
        }
        
        $this->getCode()->success("Create new a member is successfuly");
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
    
    public function removeAction(){
        //remove
        $this->entity->remove($this->getCode()->post("id"));
    }

}
