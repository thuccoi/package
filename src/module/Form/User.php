<?php

namespace module\Form;

class User {

    private $code;
    private $dm;

    //set properties code
    public function __construct($dm, \system\Helper\Code $code) {
        $this->code = $code;
        $this->dm = $dm;
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

        try {
            //new user
            $user = new \module\Model\User();

            //set information
            $user->setUsername($data->username)
                    ->setPassword($data->password)
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

            //save and send email
            $this->dm->persist($user);
            $this->dm->flush();

            $this->code->success("Register is successfuly");
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

}
