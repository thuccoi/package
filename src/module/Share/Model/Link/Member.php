<?php

namespace module\Share\Model\Link;

class Member extends \module\Share\Model\Common\AbsLink {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    private $app_entity;
    private $user_entity;

    public function __construct($connect, \system\Helper\Code $code, $config, \system\Session $session) {
        $this->init($connect, $code, $config, $session);
        //init entity app
        $this->app_entity = new \module\Share\Model\Entity\App($connect, $code, $config, $session);
        //init entity user
        $this->user_entity = new \module\Share\Model\Entity\User($connect, $code, $config, $session);
    }

    public function add($data) {

        //input app
        if (\system\Helper\Validate::isEmpty($data->app)) {
            $this->code->forbidden("app is require");
        }

        if (!\system\Helper\Validate::isString($data->app)) {
            $this->code->forbidden("type input app is not string");
        }

        $app = $this->app_entity->find($data->app);
        if (!$app) {
            $this->code->notfound("app notfound in system");
        }

        //input user
        if (\system\Helper\Validate::isEmpty($data->user)) {
            $this->code->forbidden("user is require");
        }

        if (!\system\Helper\Validate::isString($data->user)) {
            $this->code->forbidden("type input user is not string");
        }

        $user = $this->user_entity->find($data->user);
        if (!$user) {
            $this->code->notfound("user notfound in system");
        }

        //check member existed in system
        $check = $this->dm->getRepository(\module\Share\Model\Collection\Member::class)
                ->findOneBy(['app.id' => $app->getId(), 'user.id' => $user->getId()]);

        if ($check) {
            $this->code->forbidden("Member has existed in this App");
        }

        try {
            //add new member
            $member = new \module\Share\Model\Collection\Member($app, $user);

            //title
            if (isset($data->title)) {
                $member->setTitle($data->title);
            }

            //alias
            if (isset($data->alias)) {
                $member->setAlias($data->alias);
            }

            //for test 
            $this->inputTest($member, $data);

            $this->dm->persist($member);
            $this->dm->flush();

            //add new member log
            $memberlog = new \module\Share\Model\Log\Member($this->dm, $this->code, $this->config, $this->session);

            $memberlog->add((object) [
                        'user_id'  => (string) $user->getId(),
                        'app_id'   => (string) $app->getId(),
                        "metatype" => "add",
                        'message'  => "<a href='{$this->config['URL_ROOT']}/application/user/view/{$user->getId()}'>{$user->getName()}</a> đã được thêm vào ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a>"
            ]);



            //onboarding
            if (count($app->getMembers()) > 1) {
                $flat = FALSE;
                $onboarding = $app->getOnboarding('add_member');
                if ($onboarding && isset($onboarding['status'])) {
                    if ($onboarding['status'] == 0) {
                        $flat = TRUE;
                    }
                } else {
                    $flat = TRUE;
                }

                if ($flat === TRUE) {
                    $app->onboarding('add_member', 1);
                    $this->dm->persist($app);
                    $this->dm->flush();


                    //change session app onboarding
                    $this->session->set("app_onboarding", \system\Helper\ArrayCallback::select($this->session->get('app_onboarding'), function($e) {
                                if ($e['metatype'] == 'add_member') {
                                    $e['status'] = 1;
                                }
                                return $e;
                            }));
                }
            }


            return $member;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function update($id, $data) {
        $obj = $this->find($id);
        if (!$obj) {
            $this->code->notfound("Member not exist");
        }

        $editinfo = [];
        //edit alias
        if (!\system\Helper\Validate::isEmpty($data->alias) && $data->alias != $obj->getAlias()) {
            $obj->setAlias($data->alias);
            $editinfo [] = "<div class='timeline-content'><b>Biệt danh</b> của thành viên <a href='{$this->config['URL_ROOT']}/assignment/member/view/{$obj->getId()}'>{$obj->getUser()->getName()}</a> đã được đổi thành <b>{$data->alias }</b></div>";
        }


        //edit title
        if (!\system\Helper\Validate::isEmpty($data->title) && $data->title != $obj->getTitle()) {
            $obj->setTitle($data->title);
            $editinfo [] = "<div class='timeline-content'><b>Chức danh</b> của thành viên <a href='{$this->config['URL_ROOT']}/assignment/member/view/{$obj->getId()}'>{$obj->getUser()->getName()}</a> đã được đổi thành <b>{$data->title }</b></div>";
        }

        //edit description
        if (!\system\Helper\Validate::isEmpty($data->description) && $data->description != $obj->getDescription()) {
            $obj->setDescription($data->description);
            $editinfo [] = "<div class='timeline-content'><b>Mô tả</b> về thành viên <a href='{$this->config['URL_ROOT']}/assignment/member/view/{$obj->getId()}'>{$obj->getUser()->getName()}</a> đã được đổi thành <b>{$data->description }</b></div>";
        }

        //edit manager
        if (!\system\Helper\Validate::isEmpty($data->manager) && (!$obj->getManager() || $data->manager != $obj->getManager()->getId() )) {
            $manager = $this->find($data->manager);
            if (!$manager) {
                $this->code->notfound("not found manager");
            }

            if ($this->isSpiderWeb($obj, $manager)) {
                $this->code->forbidden("manager is spider web width this member");
            }

            $obj->setManager($manager);
            $editinfo [] = "<div class='timeline-content'><b>Quản lý trực tiếp</b> của thành viên <a href='{$this->config['URL_ROOT']}/assignment/member/view/{$obj->getId()}'>{$obj->getUser()->getName()}</a> đã được đổi thành <a href='{$this->config['URL_ROOT']}/assignment/member/view/{$manager->getId()}'>{$manager->getUser()->getName()}</a></div>";
        }

        //edit role to member
        if (!\system\Helper\Validate::isEmpty($data->role)) {

            if (!\system\Helper\Validate::isArray($data->role)) {
                $this->code->forbidden("role is not array");
            }

            //old roles
            $arr = $obj->getRoles();

            $oldroleids = [];
            if ($arr) {
                foreach ($arr as $val) {
                    $oldroleids[] = (string) $val->getRole()->getId();
                }
            }

            //entity link role to member
            $roletomem_link = new \module\Assignment\Model\Link\RoleToMember($this->getConnect(), $this->getCode(), $this->getConfig(), $this->session);

            //add new role to member
            foreach ($data->role as $val) {
                if (!in_array($val, $oldroleids)) {
                    $roletomem_link->add((object) [
                                "app_id"     => (string) $data->app_id,
                                "creator_id" => (string) $data->creator_id,
                                "member_id"  => $obj->getId(),
                                "role_id"    => $val
                    ]);
                }
            }

            //remove old role not in input
            foreach ($oldroleids as $val) {
                if (!in_array($val, $data->role)) {
                    $roletomem_link->remove((object) [
                                "app_id"     => (string) $data->app_id,
                                "creator_id" => (string) $data->creator_id,
                                "member_id"  => $obj->getId(),
                                "role_id"    => $val
                    ]);
                }
            }
        }

        try {
            //save and send email
            $this->dm->persist($obj);
            $this->dm->flush();

            //log create app
            foreach ($editinfo as $message) {
                $applog = new \module\Share\Model\Log\Member($this->dm, $this->code, $this->config, $this->session);
                $applog->add((object) [
                            "app_id"   => (string) $obj->getApp()->getId(),
                            "user_id"  => (string) $obj->getUser()->getId(),
                            "metatype" => "edit",
                            "message"  => $message
                ]);
            }
            return $obj;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function remove($id) {
        $obj = $this->find($id);

        if (!$obj) {
            $this->code->forbidden("Member not exist");
        }

        $user = $obj->getUser();
        $app = $obj->getApp();

        $obj->delete();

        $this->getConnect()->persist($obj);
        $this->getConnect()->flush();

        $memberlog = new \module\Share\Model\Log\Member($this->dm, $this->code, $this->config, $this->session);

        $memberlog->add((object) [
                    'user_id'  => (string) $user->getId(),
                    'app_id'   => (string) $app->getId(),
                    "metatype" => "remove",
                    'message'  => "Thành viên <a href='{$this->config['URL_ROOT']}/application/user/view/{$user->getId()}'>{$user->getName()}</a> đã được loại bỏ khỏi ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a>"
        ]);

        $this->getCode()->success("remove is successfully");
    }

    public function restore($id) {
        $obj = $this->find($id);

        if (!$obj) {
            $this->code->forbidden("Member not exist");
        }

        $user = $obj->getUser();
        $app = $obj->getApp();

        //restore
        $obj->restore();

        $this->getConnect()->persist($obj);
        $this->getConnect()->flush();

        $memberlog = new \module\Share\Model\Log\Member($this->dm, $this->code, $this->config, $this->session);

        $memberlog->add((object) [
                    'user_id'  => (string) $user->getId(),
                    'app_id'   => (string) $app->getId(),
                    "metatype" => "restore",
                    'message'  => "Thành viên <a href='{$this->config['URL_ROOT']}/application/user/view/{$user->getId()}'>{$user->getName()}</a> đã được khôi phục lại trong ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a>"
        ]);

        $this->getCode()->success("restore is successfully");
    }

    public function find($id, $type = '') {
        //find by id
        return $this->dm->getRepository(\module\Share\Model\Collection\Member::class)->find($id);
    }

    public function isSpiderWeb($employee, $manager) {
        if ($employee->getId() == $manager->getId()) {
            return true;
        }
        //check parent in loop
        do {
            $manager = $manager->getManager();

            //end
            if (!$manager) {
                return false;
            }

            //is spiderweb
            if ($employee->getId() == $manager->getId()) {
                return true;
            }
        } while ($manager);

        return false;
    }

}
