<?php

namespace module\Register\Controller;

class RegisterController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\User($connect, $code, $config, $session);
    }

    public function indexAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');

        return [];
    }

    public function createAction() {
        //get data
        $data = (object) [
                    "first_name" => $this->getCode()->post("first_name", FALSE),
                    "last_name"  => $this->getCode()->post("last_name", FALSE),
                    "username"   => $this->getCode()->post("username", FALSE),
                    "password"   => $this->getCode()->post("password", FALSE),
                    "email"      => $this->getCode()->post("email", FALSE),
                    "phone"      => $this->getCode()->post("phone", FALSE)
        ];

        //register new an user
        $this->entity->create($data);

        $this->getCode()->success("Đăng ký tài khoản thành công, mời bạn hãy vào địa chỉ email của mình để xác nhận thông tin tài khoản của mình vừa cung cấp cho chúng tôi là chính xác.");
    }

    //confirm user
    public function confirmAction() {
        //token
        $id = $this->getCode()->get("id", FALSE);
        $token = $this->getCode()->get("token", FALSE);

        $user = $this->entity->find($id);
        if ($user) {
            //check token
            if ($user->getToken() != $token) {
                $this->getCode()->error("URL đã hết hạn, hoặc sai thông tin.");
            }

            //check email confirm
            if ($user->getEmailConfirm() == $user::EMAIL_CONFIRMED) {
                $this->getCode()->error("Hành động lỗi do thông tin tài khoản này đã được xác nhận.");
            }

            //check app
            $entity_app = new \module\Share\Model\Entity\App($this->getConnect(), $this->getCode(), $this->getConfig());
            //check domain exists in application
            $domain = $this->getConfig()['DOMAIN'];
            $app = $entity_app->find($domain, 'domain');

            //check new app
            $newapp = false;
            if (!$app) {
                $data = (object) [
                            "name"     => $this->getConfig()['app']['name'],
                            "image"    => $this->getConfig()['URL_ROOT'] . $this->getConfig()['app']['image'],
                            "metatype" => $this->getConfig()['app']['metatype'],
                            "domain"   => $domain
                ];

                //create new an application
                $app = $entity_app->create($data);
                $newapp = true;
            }


            //create new member
            $entity_member = new \module\Share\Model\Link\Member($this->getConnect(), $this->getCode(), $this->getConfig());
            $data = (object) [
                        "app"  => $app->getDomain(),
                        "user" => $user->getUsername()
            ];

            $member = $entity_member->add($data);

            //check new app
            if ($newapp == true) {
                //member
                //assign owner
                $member->assignOwner();

                //activate member
                $member->activate();

                $this->getConnect()->persist($member);
                $this->getConnect()->flush();
            } else {
                //send confirm to admin
                $admins = $app->getAdmins();
                if (!$admins) {
                    $admins = $app->getOwners();
                }

                foreach ($admins as $val) {
                    //send activate email
                }
            }


            //save record
            $this->getConnect()->persist($user);
            $this->getConnect()->flush();


            $this->getCode()->success("Xác nhận thông tin thành công.");
        }

        $this->getCode()->notfound("Tài khoản không tồn tại trong hệ thống.");
    }

}
