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
        $this->entity_user = new \module\Share\Model\Entity\User($connect, $code, $config, $session);

        //init entity member
        $this->entity_member = new \module\Share\Model\Link\Member($connect, $code, $config, $session);
    }

    //for user
    public function verifyAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        return[
            "id"    => $this->getCode()->get("id", FALSE),
            "token" => $this->getCode()->get("token", FALSE)
        ];
    }

    public function confirmAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        $user = $this->entity_user->find($this->getCode()->get("id", FALSE));

        if (!$user || $user->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "user" => $user->release()
        ];
    }

    //for member
    public function memberOwnerAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        $member = $this->entity_member->find($this->getCode()->get("id", FALSE));

        if (!$member || $member->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release()
        ];
    }

    public function memberAdminAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        $member = $this->entity_member->find($this->getCode()->get("id", FALSE));

        if (!$member || $member->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release()
        ];
    }

    public function memberDefaultAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        $member = $this->entity_member->find($this->getCode()->get("id", FALSE));

        if (!$member || $member->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release()
        ];
    }

    public function memberActivateAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        $member = $this->entity_member->find($this->getCode()->get("id", FALSE));

        if (!$member || $member->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release(),
            "user"   => $member->getUser()->release(),
            "app"    => $member->getApp()->release()
        ];
    }

    public function memberDeactivateAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        $member = $this->entity_member->find($this->getCode()->get("id", FALSE));

        if (!$member || $member->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release(),
            "user"   => $member->getUser()->release(),
            "app"    => $member->getApp()->release()
        ];
    }

    //for admin
    public function adminActivateAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        $member = $this->entity_member->find($this->getCode()->get("id", FALSE));

        if (!$member || $member->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->forbidden("Link xác nhận sai hoặc hết hiệu lực", [], $this->getRouter());
        }

        return[
            "member" => $member->release(),
            "user"   => $member->getUser()->release(),
            "app"    => $member->getApp()->release()
        ];
    }

}
