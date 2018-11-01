<?php

namespace module\Register\Controller;

class NotifyController extends \system\Template\AbstractController {

    //entity user
    protected $entity_user;
    
    //entity member
    protected $entity_member;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity user
        $this->entity_user = new \module\Share\Model\Entity\User($connect, $code, $config);

        //init entity member
        $this->entity_member = new \module\Share\Model\Entity\Member($connect, $code, $config);
    }

    //for user
    public function verifyAction() {
        $this->setLayout('TAMI_NOLAYOUT');

        return[
            "id" => $this->getCode()->get("id"),
            "token" => $this->getCode()->get("token")
        ];
    }

    public function confirmAction() {
        $this->setLayout('TAMI_NOLAYOUT');

        $user = $this->entity_user->find($this->getCode()->get("id"));

        if (!$user || $user->getToken() != $this->getCode()->get("token")) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "user" => $user->release()
        ];
    }

    //for member
    public function memberOwnerAction() {
        $this->setLayout('TAMI_NOLAYOUT');

        $member = $this->entity_member->find($this->getCode()->get("id"));

        if (!$member || $member->getToken() != $this->getCode()->get("token")) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release()
        ];
    }

    public function memberAdminAction() {
        $this->setLayout('TAMI_NOLAYOUT');

        $member = $this->entity_member->find($this->getCode()->get("id"));

        if (!$member || $member->getToken() != $this->getCode()->get("token")) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release()
        ];
    }

    public function memberDefaultAction() {
        $this->setLayout('TAMI_NOLAYOUT');

        $member = $this->entity_member->find($this->getCode()->get("id"));

        if (!$member || $member->getToken() != $this->getCode()->get("token")) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release()
        ];
    }

    public function memberActivateAction() {
        $this->setLayout('TAMI_NOLAYOUT');

        $member = $this->entity_member->find($this->getCode()->get("id"));

        if (!$member || $member->getToken() != $this->getCode()->get("token")) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release()
        ];
    }

}
