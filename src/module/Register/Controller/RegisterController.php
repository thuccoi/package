<?php

namespace module\Register\Controller;

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
        $entity = new \module\Share\Model\Entity\User($this->getConnect(), $this->getCode());
        //register new an user
        $entity->create($data);
    }

}
