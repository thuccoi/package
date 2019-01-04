<?php

namespace module\Share\Model\Link;

class Member extends \module\Share\Model\Common\AbsLink {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    private $app_entity;
    private $user_entity;

    public function __construct($connect, \system\Helper\Code $code, $config) {
        $this->init($connect, $code, $config);
        //init entity app
        $this->app_entity = new \module\Share\Model\Entity\App($connect, $code, $config);
        //init entity user
        $this->user_entity = new \module\Share\Model\Entity\User($connect, $code, $config);
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
            //add new memeber
            $member = new \module\Share\Model\Collection\Member($app, $user);

            //title
            if (isset($data->title)) {
                $member->setTitle($data->title);
            }

            //alias
            if (isset($data->alias)) {
                $member->setAlias($data->alias);
            }

            $this->dm->persist($member);
            $this->dm->flush();

            return $member;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function update($id, $data) {
        
    }

    public function remove($id) {
        
    }

    public function restore($id) {
        
    }

    public function find($id, $type = '') {
        //find by id
        return $this->dm->getRepository(\module\Share\Model\Collection\Member::class)->find($id);
    }

    //assign member
    public function assign($id, $role) {
        //get member
        $member = $this->find($id);
        if (!$member) {
            $this->getCode()->notfound("Not found member in apps");
        }

        //assign
        switch ($role) {
            case \module\Share\Model\Collection\Member::ROLE_OWNER:
                //check owner
                if ($member->isOwner()) {
                    $this->code->error("User was Owner");
                }

                //assign owner
                $member->assignOwner($this->config);
                break;
            case \module\Share\Model\Collection\Member::ROLE_ADMIN:
                //check admin
                if ($member->isAdmin()) {
                    $this->code->error("User was Admin");
                }

                //assign admin
                $member->assignAdmin($this->config);
                break;
            case \module\Share\Model\Collection\Member::ROLE_DEFAULT:
                //check default
                if ($member->isDefault()) {
                    $this->code->error("User was Default");
                }

                //assign default
                $member->assignDefault($this->config);
                break;
        }

        $this->dm->persist($member);
        $this->dm->flush();

        $this->code->success("Assign member success");
    }

}