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
        $user = $this->entity->find($this->getCode()->post("id"));
        if ($user) {

            //check password
            if (!$user->authLogin($this->getCode()->post("password"))) {
                $this->getCode()->forbidden("Sai mật khẩu đăng nhập");
            }

            //check status deactivate
            if ($user->isDeactivate()) {
                $this->getCode()->forbidden("Tài khoản này đã bị cấm hoạt động trong hệ thống");
            }

            //check status activate
            if (!$user->isActivate()) {
                $this->getCode()->forbidden("Tài khoản của bạn chưa được kích hoạt, bạn hãy liên hệ với người quản trị để được hỗ trợ.");
            }

            $this->getCode()->success("Đăng nhập thành công");
        }

        $this->getCode()->notfound("Tên tài khoản này không tồn tại trong hệ thống.");
    }

}
