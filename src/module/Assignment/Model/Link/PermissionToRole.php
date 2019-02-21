<?php

namespace module\Assignment\Model\Link;

class PermissionToRole extends \module\Share\Model\Common\AbsLink {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    private $role_entity;

    public function __construct($connect, \system\Helper\Code $code, $config) {
        $this->init($connect, $code, $config);
        //init entity app
        $this->role_entity = new \module\Assignment\Model\Entity\Role($connect, $code, $config);
    }

    public function add($data) {
        //input app id 
        if (\system\Helper\Validate::isEmpty($data->app_id)) {
            $this->code->forbidden("app_id is require");
        }

        //input creator id 
        if (\system\Helper\Validate::isEmpty($data->creator_id)) {
            $this->code->forbidden("creator_id is require");
        }

        //input permission
        if (\system\Helper\Validate::isEmpty($data->permission)) {
            $this->code->forbidden("permission is require");
        }

        if (!\system\Helper\Validate::isString($data->permission)) {
            $this->code->forbidden("type input permission is not string");
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

        //check permission existed in role
        $count = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\PermissionToRole::class)
                ->field('permission')->equals($data->permission)
                ->field('role.id')->equals($data->role_id)
                ->field('app_id')->equals($data->app_id)
                ->count()
                ->getQuery()
                ->execute();

        if ($count) {
            $this->code->forbidden("Permission has existed in this Role");
        }

        try {

            $obj = new \module\Assignment\Model\Collection\PermissionToRole();
            $obj->setPermission($data->permission)
                    ->setRole($role)
                    ->setAppId($data->app_id)
                    ->setCreatorId($data->creator_id);


            $this->dm->persist($obj);
            $this->dm->flush();

            //name array
            $pername = [];
            foreach ($this->getConfig()['account_member']['permissions'] as $val) {
                $pername[$val['value']] = $val['name'];
            }


            //add new member log
            $log = new \module\Assignment\Model\Log\PermissionToRole($this->dm, $this->code, $this->config);

            if (isset($pername[$data->permission])) {
                $log->add((object) [
                            'role_id'    => (string) $role->getId(),
                            'app_id'     => $data->app_id,
                            'creator_id' => $data->creator_id,
                            "metatype"   => "add",
                            'message'    => "Vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$role->getId()}'>{$role->getName()}</a> đã được phân quyền <b>{$pername[$data->permission]}</b>"
                ]);
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

        $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\PermissionToRole::class)
                ->field('role.id')->equals($data->role_id)
                ->field('app_id')->equals($data->app_id)
                ->remove()
                ->getQuery()
                ->execute();


        //name array
        $pername = [];
        foreach ($this->getConfig()['account_member']['permissions'] as $val) {
            $pername[$val['value']] = $val['name'];
        }


        //add new log
        $log = new \module\Assignment\Model\Log\PermissionToRole($this->dm, $this->code, $this->config);

        if (isset($pername[$data->permission])) {
            $log->add((object) [
                        'role_id'    => $data->role_id,
                        'app_id'     => $data->app_id,
                        'creator_id' => $data->creator_id,
                        "metatype"   => "remove",
                        'message'    => "Vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$role->getId()}'>{$role->getName()}</a> đã bị lấy đi quyền <b>{$pername[$data->permission]}</b>"
            ]);
        }
    }

    public function restore($id) {
        
    }

    public function find($id, $type = '') {
        //find by id
        return $this->dm->getRepository(\module\Share\Model\Collection\Member::class)->find($id);
    }

}
