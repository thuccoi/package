<?php

namespace module\Share\Model\Entity;

class User {

    private $code;
    private $dm;
    private $tami_config;

    //set properties code
    public function __construct($dm, \system\Helper\Code $code, $config) {
        $this->code = $code;
        $this->dm = $dm;

        $this->tami_config = $config;
    }

    public function create($data) {

        //field required
        //first name
        if (\system\Helper\Validate::isEmpty($data->first_name)) {
            $this->code->forbidden("first_name is require");
        }

        if (!\system\Helper\Validate::isString($data->first_name)) {
            $this->code->forbidden("first_name was not string");
        }

        //last name
        if (\system\Helper\Validate::isEmpty($data->last_name)) {
            $this->code->forbidden("last_name is require");
        }

        if (!\system\Helper\Validate::isString($data->last_name)) {
            $this->code->forbidden("last_name was not string");
        }

        //username
        if (\system\Helper\Validate::isEmpty($data->username)) {
            $this->code->forbidden("username is require");
        }

        if (!\system\Helper\Validate::isString($data->username)) {
            $this->code->forbidden("username was not string");
        }

        //check username is email
        if (\system\Helper\Validate::isEmail($data->username)) {
            $this->code->forbidden("username can not be email");
        }


        //password
        if (\system\Helper\Validate::isEmpty($data->password)) {
            $this->code->forbidden("password is require");
        }

        if (!\system\Helper\Validate::isString($data->password)) {
            $this->code->forbidden("password was not string");
        }

        //email
        if (\system\Helper\Validate::isEmpty($data->email)) {
            $this->code->forbidden("email is require");
        }

        if (!\system\Helper\Validate::isEmail($data->email)) {
            $this->code->notfound("email was not valid email");
        }

        //check exists username
        if ($this->find($data->username)) {
            $this->code->forbidden("username was existed in system");
        }

        //check exists email
        if ($this->find($data->email)) {
            $this->code->forbidden("email was existed in system");
        }

        try {
            //new user
            $user = new \module\Share\Model\Collection\User();

            //set information
            $user->setUsername($data->username)
                    ->setPassword($data->password, $this->tami_config)
                    ->setFirstName($data->first_name)
                    ->setLastName($data->last_name)
                    ->setEmail($data->email);

            //isset phone
            if (!\system\Helper\Validate::isEmpty($data->phone)) {

                //check phone is string
                if (!\system\Helper\Validate::isString($data->phone)) {
                    $this->code->notfound("phone was not string");
                }

                $user->setPhone($data->phone);
            }
            
            //send email welcome
            $user->sendWelcomeEmail($this->tami_config);
            
            //save and send email
            $this->dm->persist($user);
            $this->dm->flush();

            $this->code->success("Register is successfuly");
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function find($id) {
        //find by id
        $find = $this->dm->getRepository(\module\Share\Model\Collection\User::class)->find($id);

        //find by username
        if (!$find) {

            $find = $this->dm->getRepository(\module\Share\Model\Collection\User::class)->findOneBy(['username' => $id]);
        }

        //find by email
        if (!$find) {
            $find = $this->dm->getRepository(\module\Share\Model\Collection\User::class)->findOneBy(['email' => $id]);
        }

        return $find;
    }

}
