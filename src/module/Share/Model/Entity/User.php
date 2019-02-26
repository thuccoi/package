<?php

namespace module\Share\Model\Entity;

class User extends \module\Share\Model\Common\AbsEntity {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    //set properties code
    public function __construct($connect, \system\Helper\Code $code, $config, \system\Session $session) {

        // $dm is a DocumentManager instance we should already have
        $this->init($connect, $code, $config, $session);
    }

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

            //log create user
            $userlog = new \module\Share\Model\Log\User($this->dm, $this->code, $this->config, $this->session);
            $userlog->add((object) [
                        "user_id"  => (string) $user->getId(),
                        "metatype" => "create",
                        "message"  => "Người dùng <a href='{$this->config['URL_ROOT']}/application/user/view/{$user->getId()}'>{$user->getName()}</a> đã được tạo mới"
            ]);

            return $user;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Lỗi cơ sở dữ liệu");
    }

    public function edit($id, $data) {
        
    }

    public function delete($id) {
        $obj = $this->find($id);
        if ($obj) {
            foreach ($obj->getMembers() as $val) {
                $val->delete();
                $this->dm->persist($val);
            }

            $obj->delete();
            $this->dm->persist($obj);
            $this->dm->flush();
        }
    }

    public function restore($id) {
        $obj = $this->find($id);
        if ($obj) {
            foreach ($obj->getMembers() as $val) {
                $val->restore();
                $this->dm->persist($val);
            }

            $obj->restore();
            $this->dm->persist($obj);
            $this->dm->flush();
        }
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
