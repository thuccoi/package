<?php

namespace module\Login\Controller;

class LoginController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\User($connect, $code, $config);
    }

    public function indexAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        if ($this->getViewer() && $this->getViewer()->auth == 1) {
            //get home
            $home = $this->getConfig('home');

            $this->getRouter()->redirect($home['module'], ['controller' => $home['controller'], 'action' => $home['action']]);
        }
    }

    public function loginAction() {
        //get user
        $user = $this->entity->find($this->getCode()->post("email", FALSE));
        if ($user) {

            //check email confirm
            if (!$user->isEmailConfirm()) {
                $this->getCode()->forbidden("Tài khoản này chưa được xác nhận qua Email, bạn hãy vào hòm thư của mình để thực hiện xác nhận tài khoản");
            }

            //check password
            if (!$user->authLogin($this->getCode()->post("password", FALSE))) {
                $this->getCode()->forbidden("Sai mật khẩu đăng nhập");
            }

            //get members
            $members = $user->getMembers();

            foreach ($members as $val) {
                $app = $val->getApp();
                if ($app->getDomain() == $this->getConfig()['DOMAIN']) {
                    //check deactivate
                    if ($val->isDeactivate()) {
                        $this->getCode()->forbidden("Thành viên này đang bị cấm hoạt động");
                    }

                    //check activate
                    if ($val->isActivate()) {

                        //setViewer session
                        $val->setViewer($this->getSession());

                        $this->getCode()->success("Đăng nhập thành công", [], $this->urlInside("application"));
                    }
                }
            }

            $this->getCode()->forbidden("Thành viên này chưa được kích hoạt, bạn hãy liên hệ với người quản trị của ứng dụng để được hỗ trợ.");
        }

        $this->getCode()->notfound("Tên tài khoản hoặc email không tồn tại trong hệ thống.");
    }

    public function forgotPasswordAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        return [];
    }

    public function newPasswordAction() {
        $user = $this->entity->find($this->getCode()->post("email", FALSE), 'email');
        if ($user) {

            //check email confirm
            if (!$user->isEmailConfirm()) {
                $this->getCode()->forbidden("Tài khoản này chưa được xác nhận qua Email, bạn hãy vào hòm thư của mình để thực hiện xác nhận tài khoản");
            }

            $this->getCode()->success("Chúng tôi đã gửi một link tạo mật khẩu mới vào hòm thư của bạn. Bạn hãy làm theo hướng dẫn của chúng tôi trong đó để nhận được mật khẩu mới của mình.");
        }
        $this->getCode()->notfound("Địa chỉ Email không tồn tại trong hệ thống.");
    }

}
