<?php

namespace module\Controller;

class RegisterController extends \system\Template\AbstractController {

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

        //input form
        $form = new \module\Form\User($this->getConnect(), $this->getCode());
        //register new an user
        $form->create($data);
    }

}
