<?php

namespace module\Register\Controller;

class RegisterController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\User($connect, $code, $config);
    }

    public function createAction() {
        //get data
        $data = (object) [
                    "first_name" => $this->getCode()->post("first_name"),
                    "last_name" => $this->getCode()->post("last_name"),
                    "username" => $this->getCode()->post("username"),
                    "password" => $this->getCode()->post("password"),
                    "email" => $this->getCode()->post("email"),
                    "phone" => $this->getCode()->post("phone"),
                    "application" => $this->getCode()->post("application"),
        ];

        //register new an user
        $this->entity->create($data);
    }

    //confirm user
    public function confirmAction() {
        //token
        $id = $this->getCode()->get("id");
        $token = $this->getCode()->get("token");

        $obj = $this->entity->find($id);
        if ($obj) {
            //check token
            if ($obj->getToken() != $token) {
                $this->getCode()->error("URL đã hết hạn, hoặc sai thông tin.", [], $this->getRouter());
            }

            //check email confirm
            if ($obj->getEmailConfirm() == $obj::EMAIL_CONFIRMED) {
                $this->getCode()->error("Hành động lỗi do thông tin tài khoản này đã được xác nhận.", [], $this->getRouter());
            }

            //activate account
            $obj->sendConfirmEmail($this->getConfig());

            //save record
            $this->getConnect()->persist($obj);
            $this->getConnect()->flush();


            $this->getCode()->success("Xác nhận thông tin thành công.", [], $this->getRouter());
        }

        $this->getCode()->notfound("Tài khoản không tồn tại trong hệ thống.", [], $this->getRouter());
    }

}
