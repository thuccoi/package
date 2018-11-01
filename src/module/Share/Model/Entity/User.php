<?php

namespace module\Share\Model\Entity;

class User extends \module\Share\Model\Common\AbsEntity {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    public function create($data) {

        //field required
        //first name
        if (\system\Helper\Validate::isEmpty($data->first_name)) {
            $this->code->forbidden("Nhập vào tên");
        }

        if (!\system\Helper\Validate::isString($data->first_name)) {
            $this->code->forbidden("Tên phải là một chuỗi");
        }

        //last name
        if (\system\Helper\Validate::isEmpty($data->last_name)) {
            $this->code->forbidden("Nhập vào họ");
        }

        if (!\system\Helper\Validate::isString($data->last_name)) {
            $this->code->forbidden("Họ phải là một chuỗi");
        }

        //username
        if (\system\Helper\Validate::isEmpty($data->username)) {
            $this->code->forbidden("Nhập vào tên tài khoản");
        }

        if (!\system\Helper\Validate::isString($data->username)) {
            $this->code->forbidden("Tên tài khoản phải là một chuỗi");
        }

        //check username is email
        if (\system\Helper\Validate::isEmail($data->username)) {
            $this->code->forbidden("Tên tài khoản không được là địa chỉ Email");
        }


        //password
        if (\system\Helper\Validate::isEmpty($data->password)) {
            $this->code->forbidden("Mật khẩu được yêu cầu");
        }

        if (!\system\Helper\Validate::isString($data->password)) {
            $this->code->forbidden("Mật khẩu phải một chuỗi");
        }

        //email
        if (\system\Helper\Validate::isEmpty($data->email)) {
            $this->code->forbidden("Địa chỉ Email được yêu cầu");
        }

        if (!\system\Helper\Validate::isEmail($data->email)) {
            $this->code->notfound("Địa chỉ Email không đúng định dạng");
        }

        //check exists username
        if ($this->find($data->username, 'username')) {
            $this->code->forbidden("Tên tài khoản đã tồn tại trong hệ thống");
        }

        //check exists email
        if ($this->find($data->email, 'email')) {
            $this->code->forbidden("Địa chỉ Email đã tồn tại trong một tài khoản khác của hệ thống");
        }


        try {
            //new user
            $user = new \module\Share\Model\Collection\User();

            //set information
            $user->setUsername($data->username)
                    ->setPassword($data->password, $this->config)
                    ->setFirstName($data->first_name)
                    ->setLastName($data->last_name)
                    ->setEmail($data->email);

            //isset phone
            if (!\system\Helper\Validate::isEmpty($data->phone)) {

                //check phone is string
                if (!\system\Helper\Validate::isString($data->phone)) {
                    $this->code->notfound("Số điện thoại phải là một chuỗi");
                }

                $user->setPhone($data->phone);
            }

            //save and send email
            $this->dm->persist($user);
            $this->dm->flush();

            //check app
            $entity_app = new App($this->dm, $this->code, $this->config);
            //check domain exists in application
            $domain = $this->config['DOMAIN'];
            $app = $entity_app->find($domain, 'domain');
            if (!$app) {
                $data = (object) [
                            "name" => $this->config['app']['name'],
                            "metatype" => $this->config['app']['metatype'],
                            "domain" => $domain
                ];

                //create new an application
                $app = $entity_app->create($data);
            }


            //create new member
            $entity_member = new Member($this->dm, $this->code, $this->config);
            $data = (object) [
                        "app" => $app->getMetatype(),
                        "user" => $user->getUsername()
            ];
            $member = $entity_member->create($data);

            //send verify email
            $user->sendVerifyEmail($this->config);

            return $user;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Lỗi cơ sở dữ liệu");
    }

    public function find($id, $type = '') {
        switch ($type) {
            case 'username':
                return $this->dm->getRepository(\module\Share\Model\Collection\User::class)->findOneBy(['username' => $id]);
                break;
            case 'email':
                return $this->dm->getRepository(\module\Share\Model\Collection\User::class)->findOneBy(['email' => $id]);
                break;
            default :
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
                break;
        }
    }

}
