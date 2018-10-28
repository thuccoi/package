<?php

namespace Account\Controller;

class RegisterController extends \system\Template\AbstractController {

    public function createAction() {
        //get connect
        $dm = $this->getConnect();
        //new user
        $user = new \Account\Model\User();

        //set information
        $user->setUsername("thuccoi")
                ->setPassword("lr3fgkRh5")
                ->setFirstName("thuc")
                ->setLastName("nguyen the")
                ->setEmail("thucfami@gmail.com")
                ->setPhone("0979 846 286");

        //save and send email
        $dm->persist($user);
        $dm->flush();

        //release ajax
        $this->getCode()->success("Register is successfuly");
    }

}
