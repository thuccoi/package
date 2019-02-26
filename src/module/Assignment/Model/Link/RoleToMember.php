<?php

namespace module\Assignment\Model\Link;

class RoleToMember extends \module\Share\Model\Common\AbsLink {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    private $role_entity;
    private $member_entity;
    private $app_entity;

    public function __construct($connect, \system\Helper\Code $code, $config, \system\Session $session) {
        $this->init($connect, $code, $config, $session);
        //init entity app
        $this->role_entity = new \module\Assignment\Model\Entity\Role($connect, $code, $config, $session);
        //init entity user
        $this->member_entity = new \module\Share\Model\Link\Member($connect, $code, $config, $session);


        //init entity app
        $this->app_entity = new \module\Share\Model\Entity\App($connect, $code, $config, $session);
    }

    public function add($data) {
        //input app id 
        if (\system\Helper\Validate::isEmpty($data->app_id)) {
            $this->code->forbidden("app_id is require");
        }

        $app = $this->app_entity->find($data->app_id);
        if (!$app) {
            $this->code->notfound("app notfound in system");
        }

        //input creator id 
        if (\system\Helper\Validate::isEmpty($data->creator_id)) {
            $this->code->forbidden("creator_id is require");
        }

        //input member
        if (\system\Helper\Validate::isEmpty($data->member_id)) {
            $this->code->forbidden("member_id is require");
        }

        if (!\system\Helper\Validate::isString($data->member_id)) {
            $this->code->forbidden("member_id input app is not string");
        }

        $member = $this->member_entity->find($data->member_id);
        if (!$member) {
            $this->code->notfound("member notfound in system");
        }

        //input role
        if (\system\Helper\Validate::isEmpty($data->role_id)) {
            $this->code->forbidden("role_id is require");
        }

        if (!\system\Helper\Validate::isString($data->role_id)) {
            $this->code->forbidden("type input role_id is not string");
        }

        $role = $this->role_entity->find($data->role_id);
        if (!$role) {
            $this->code->notfound("role notfound in system");
        }

        //check member existed in system
        $count = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleToMember::class)
                ->field('member.id')->equals($data->member_id)
                ->field('role.id')->equals($data->role_id)
                ->field('app_id')->equals($data->app_id)
                ->count()
                ->getQuery()
                ->execute();

        if ($count) {
            $this->code->forbidden("Role has existed in this Member");
        }

        try {

            $obj = new \module\Assignment\Model\Collection\RoleToMember();

            $obj->setAppId($data->app_id);
            $obj->setCreatorId($data->creator_id);

            $obj->setMember($member);
            $obj->setRole($role);

            //for test
            $this->inputTest($obj, $data);

            $this->getConnect()->persist($obj);
            $this->getConnect()->flush();

            //add new member log
            $log = new \module\Assignment\Model\Log\RoleToMember($this->dm, $this->code, $this->config, $this->session);

            $log->add((object) [
                        'role_id'    => (string) $role->getId(),
                        'member_id'  => (string) $member->getId(),
                        'app_id'     => $data->app_id,
                        'creator_id' => $data->creator_id,
                        "metatype"   => "add",
                        'message'    => "Thành viên <a href='{$this->config['URL_ROOT']}/assignment/member/view/{$member->getId()}'>{$member->getUser()->getName()}</a> đã được giao cho vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$role->getId()}'>{$role->getName()}</a>"
            ]);


            //onboarding
            $flat = FALSE;
            $onboarding = $app->getOnboarding('assginment');
            if ($onboarding && isset($onboarding['status'])) {
                if ($onboarding['status'] == 0) {
                    $flat = TRUE;
                }
            } else {
                $flat = TRUE;
            }

            if ($flat === TRUE) {
                $app->onboarding('assginment', 1);
                $this->dm->persist($app);
                $this->dm->flush();
            }



            return $obj;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function update($id, $data) {
        
    }

    public function remove($data) {
        //input app id 
        if (\system\Helper\Validate::isEmpty($data->app_id)) {
            $this->code->forbidden("app_id is require");
        }


        //input role id 
        if (\system\Helper\Validate::isEmpty($data->role_id)) {
            $this->code->forbidden("role_id is require");
        }

        if (!\system\Helper\Validate::isString($data->role_id)) {
            $this->code->forbidden("type input role_id is not string");
        }

        $role = $this->role_entity->find($data->role_id);
        if (!$role) {
            $this->code->notfound("role notfound in system");
        }

        //input member id 
        if (\system\Helper\Validate::isEmpty($data->member_id)) {
            $this->code->forbidden("member_id is require");
        }

        if (!\system\Helper\Validate::isString($data->member_id)) {
            $this->code->forbidden("type input member_id is not string");
        }


        $member = $this->member_entity->find($data->member_id);
        if (!$member) {
            $this->code->notfound("member notfound in system");
        }

        $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleToMember::class)
                ->field('member.id')->equals($data->member_id)
                ->field('role.id')->equals($data->role_id)
                ->field('app_id')->equals($data->app_id)
                ->remove()
                ->getQuery()
                ->execute();

        //add new log
        $log = new \module\Assignment\Model\Log\RoleToMember($this->dm, $this->code, $this->config, $this->session);

        $log->add((object) [
                    'role_id'    => $data->role_id,
                    'member_id'  => $data->member_id,
                    'app_id'     => $data->app_id,
                    'creator_id' => $data->creator_id,
                    "metatype"   => "remove",
                    'message'    => "Thành viên <a href='{$this->config['URL_ROOT']}/assignment/member/view/{$member->getId()}'>{$member->getUser()->getName()}</a> đã bị lấy đi vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$role->getId()}'>{$role->getName()}</a>"
        ]);

        return true;
    }

    public function restore($id) {
        
    }

    public function find($id, $type = '') {
        //find by id
        return $this->dm->getRepository(\module\Share\Model\Collection\Member::class)->find($id);
    }

}
