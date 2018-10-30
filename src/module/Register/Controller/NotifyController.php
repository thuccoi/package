<?php

namespace module\Register\Controller;

class NotifyController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\User($connect, $code, $config);
    }

    public function registerAction() {
        $this->setLayout('TAMI_NOLAYOUT');

        return[
            "id" => $this->getCode()->get("id"),
            "token" => $this->getCode()->get("token")
        ];
    }

    public function confirmAction() {
        $this->setLayout('TAMI_NOLAYOUT');

        $user = $this->entity->find($this->getCode()->get("id"));

        if (!$user || $user->getToken() != $this->getCode()->get("token")) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực");
        }

        return[
            "user" => $user->release()
        ];
    }

}
