<?php

namespace module\Register\Controller;

class AdminController extends \system\Template\AbstractController {

    //entity member
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Link\Member($connect, $code, $config);
    }

    //activate member
    public function activateAction() {
        //check query param
        $member = $this->entity->find($this->getCode()->get("id", FALSE));
        if (!$member || $member->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->notfound("Link hết hạn, hoặc sai", [], $this->getRouter());
        }

        if ($member->isActivate()) {
            $this->getCode()->error("Tài khoản đã được kích hoạt", [], $this->getRouter());
        }

        //activate member
        $member->activate($this->getConfig());

        $this->getConnect()->persist($member);
        $this->getConnect()->flush();

        $this->getCode()->success("Kích hoạt tài khoản thành viên thành công", [], $this->getRouter());
    }

    //deactivate member
    public function deactivateAction() {
        //check query param
        $member = $this->entity->find($this->getCode()->get("id", FALSE));
        if (!$member || $member->getToken() != $this->getCode()->get("token", FALSE)) {
            $this->getCode()->notfound("Link hết hạn, hoặc sai", [], $this->getRouter());
        }

        if ($member->isDeactivate()) {
            $this->getCode()->error("Tài khoản đã được cấm hoạt động", [], $this->getRouter());
        }

        //activate member
        $member->deactivate();

        $this->getConnect()->persist($member);
        $this->getConnect()->flush();

        $this->getCode()->success("Từ chối hoạt động tài khoản thành viên thành công", [], $this->getRouter());
    }

}
