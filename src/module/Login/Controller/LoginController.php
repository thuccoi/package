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
        
    }

    public function loginAction() {
        //get user
        $user = $this->entity->find($this->getCode()->post("email"));
        if ($user) {

            //check email confirm
            if (!$user->isEmailConfirm()) {
                $this->getCode()->forbidden("Tài khoản này chưa được xác nhận qua Email, bạn hãy vào hòm thư của mình để thực hiện xác nhận tài khoản");
            }

            //check password
            if (!$user->authLogin($this->getCode()->post("password"))) {
                $this->getCode()->forbidden("Sai mật khẩu đăng nhập");
            }
       
            $this->getCode()->success("Đăng nhập thành công");
        }

        $this->getCode()->notfound("Tên tài khoản hoặc email không tồn tại trong hệ thống.");
    }

    public function forgotPasswordAction() {
        
    }

    public function newPasswordAction() {
        $user = $this->entity->find($this->getCode()->post("email"), 'email');
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
