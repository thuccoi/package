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
                    "phone" => $this->getCode()->post("phone")
        ];

        //register new an user
        $this->entity->create($data);
    }

    //active user
    public function activateAction() {
        //token
        $id = $this->getCode()->get("id");
        $token = $this->getCode()->get("token");

        $obj = $this->entity->find($id);
        if ($obj) {
            //check token
            if ($obj->getToken() != $token) {
                $this->getCode()->error("URL đã hết hạn, hoặc sai thông tin.");
            }

            //check status
            if ($obj->getStatus() == $obj::STATUS_ACTIVATE) {
                $this->getCode()->error("Hành động lỗi do tài khoản này đã được kích hoạt.");
            }

            //activate account
            $obj->activate();
            
            //save record
            $this->getConnect()->persist($obj);
            $this->flush();
        }

        $this->getCode()->notfound("Tài khoản không tồn tại trong hệ thống.");
    }

}
