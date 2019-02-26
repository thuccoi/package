<?php

namespace module\Assignment\Model\Entity;

class Role extends \module\Share\Model\Common\AbsEntity {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    private $app_entity;

    //set properties code
    public function __construct($connect, \system\Helper\Code $code, $config) {

        // $dm is a DocumentManager instance we should already have
        $this->init($connect, $code, $config);

        //init entity app
        $this->app_entity = new \module\Share\Model\Entity\App($connect, $code, $config);
    }

    public function create($data) {

        //field required
        //app_id
        if (\system\Helper\Validate::isEmpty($data->app_id)) {
            $this->code->forbidden("app_id is require");
        }

        $app = $this->app_entity->find($data->app_id);
        if (!$app) {
            $this->code->notfound("app notfound in system");
        }

        //creator_id
        if (\system\Helper\Validate::isEmpty($data->creator_id)) {
            $this->code->forbidden("creator_id is require");
        }

        //name
        if (\system\Helper\Validate::isEmpty($data->name)) {
            $this->code->forbidden("name is require");
        }

        if (!\system\Helper\Validate::isString($data->name)) {
            $this->code->forbidden("name was not string");
        }

        //metatype
        $data->metatype = \system\Helper\Str::toMetatype($data->name);


        //check existed
        if ($this->find($data->metatype, 'metatype')) {
            $this->code->forbidden("metatype is existed in system");
        }

        //parent
        if (\system\Helper\Validate::isEmpty($data->parent)) {
            $this->code->forbidden("parent is require");
        }

        if (!\system\Helper\Validate::isString($data->parent)) {
            $this->code->forbidden("parnet was not string");
        }

        $parent = $this->find($data->parent);
        if (!$parent) {
            $this->code->notfound("parent is notfound in this application");
        }

        try {
            //new obj
            $obj = new \module\Assignment\Model\Collection\Role();

            //set information
            $obj->setName($data->name)
                    ->setMetatype($data->metatype)
                    ->setParent($parent)
                    ->setAppId($data->app_id)
                    ->setCreatorId($data->creator_id);

            //description
            if (\system\Helper\Validate::isString($data->description)) {
                $obj->setDescription($data->description);
            }

            //for test
            $this->inputTest($obj, $data);

            //save 
            $this->dm->persist($obj);
            $this->dm->flush();

            //log create role
            $log = new \module\Assignment\Model\Log\Role($this->dm, $this->code, $this->config);
            $log->add((object) [
                        "role_id"    => (string) $obj->getId(),
                        "metatype"   => "create",
                        "message"    => "Vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được tạo mới",
                        "app_id"     => $data->app_id,
                        "creator_id" => $data->creator_id
            ]);

            //permission to role entity
            $perentity = new \module\Assignment\Model\Link\PermissionToRole($this->getConnect(), $this->getCode(), $this->getConfig());

            //add permission
            if (\system\Helper\Validate::isArray($data->permission)) {
                foreach ($data->permission as $val) {

                    //add permission
                    $perentity->add((object) [
                                "permission" => $val,
                                "role_id"    => (string) $obj->getId(),
                                "app_id"     => $data->app_id,
                                "creator_id" => $data->creator_id
                    ]);
                }
            }


            //onboarding
            $flat = FALSE;
            $onboarding = $app->getOnboarding('create_role');
            if ($onboarding && isset($onboarding['status'])) {
                if ($onboarding['status'] == 0) {
                    $flat = TRUE;
                }
            } else {
                $flat = TRUE;
            }

            if ($flat === TRUE) {
                $app->onboarding('create_role', 1);
                $this->dm->persist($app);
                $this->dm->flush();
            }



            return $obj;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function edit($id, $data) {
        $obj = $this->find($id);
        if ($obj) {
            $editinfo = [];
            //edit name
            if (!\system\Helper\Validate::isEmpty($data->name) && $data->name != $obj->getName()) {

                $editinfo [] = "<div class='timeline-content'>Tên của vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được đổi thành <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$data->name }</a></div>";

                $obj->setName($data->name);
            }

            //edit description
            if (!\system\Helper\Validate::isEmpty($data->description) && $data->description != $obj->getDescription()) {

                $editinfo [] = "<div class='timeline-content'>Mô tả của vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được đổi thành <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$data->description }</a></div>";
                $obj->setDescription($data->description);
            }

            //edit parent
            if (!\system\Helper\Validate::isEmpty($data->parent)) {
                if (($obj->getParent() && $obj->getParent()->getId() != $data->parent) || !$obj->getParent()) {
                    $parent = $this->find($data->parent);
                    if (!$parent) {
                        $this->code->notfound("parent is notfound in this application");
                    }

                    //check spiderweb
                    if ($this->isSpiderweb($obj, $parent)) {
                        $this->code->forbidden("parent and this role is spiderweb");
                    }

                    $obj->setParent($parent);

                    $editinfo [] = "<div class='timeline-content'>Cha của vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được đổi thành <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$parent->getName() }</a></div>";
                }
            }

            //edit permission
            if (\system\Helper\Validate::isArray($data->permission)) {
                $oldper = $obj->getPermissions();

                //permission to role entity
                $perentity = new \module\Assignment\Model\Link\PermissionToRole($this->getConnect(), $this->getCode(), $this->getConfig());

                //add new
                foreach ($data->permission as $val) {
                    if (!in_array($val, $oldper)) {

                        //add permission
                        $perentity->add((object) [
                                    "permission" => $val,
                                    "role_id"    => (string) $obj->getId(),
                                    "app_id"     => $data->app_id,
                                    "creator_id" => $data->creator_id
                        ]);
                    }
                }

                //remove old
                foreach ($oldper as $val) {
                    if (!in_array($val, $data->permission)) {
                        //add permission
                        $perentity->remove((object) [
                                    "permission" => $val,
                                    "role_id"    => (string) $obj->getId(),
                                    "app_id"     => $data->app_id,
                                    "creator_id" => $data->creator_id
                        ]);
                    }
                }
            }

            $this->dm->persist($obj);
            $this->dm->flush();

            //log create app
            foreach ($editinfo as $message) {
                $applog = new \module\Assignment\Model\Log\Role($this->dm, $this->code, $this->config);
                $applog->add((object) [
                            "role_id"    => (string) $obj->getId(),
                            "metatype"   => "edit",
                            "message"    => $message,
                            "app_id"     => $data->app_id,
                            "creator_id" => $data->creator_id
                ]);
            }

            return $obj;
        } else {
            $this->code->notfound("Role not exists in system");
        }
    }

    public function delete($id) {
        $obj = $this->find($id);
        if ($obj) {
//            foreach ($obj->getMembers() as $val) {
//                $val->delete();
//                $this->dm->persist($val);
//            }

            $obj->delete();
            $this->dm->persist($obj);
            $this->dm->flush();
            return true;
        } else {
            $this->code->notfound("Role not exists in system");
        }
    }

    public function restore($id) {
        $obj = $this->find($id);
        if ($obj) {
//            foreach ($obj->getMembers() as $val) {
//                $val->restore();
//                $this->dm->persist($val);
//            }

            $obj->restore();
            $this->dm->persist($obj);
            $this->dm->flush();
            return true;
        } else {
            $this->code->notfound("Role not exists in system");
        }
    }

    public function find($id, $type = '') {
        switch ($type) {
            case 'metatype':
                return $this->dm->getRepository(\module\Assignment\Model\Collection\Role::class)->findOneBy(['metatype' => $id]);
            default :
                //find by id
                $find = $this->dm->getRepository(\module\Assignment\Model\Collection\Role::class)->find($id);
                return $find;
        }

        return null;
    }

    //check is spiderweb
    public function isSpiderweb($obj, $parent) {
        if ($obj->getId() == $parent->getId()) {
            return true;
        }
        //check parent in loop
        do {
            $parent = $parent->getParent();

            //end
            if (!$parent) {
                return false;
            }

            //is spiderweb
            if ($obj->getId() == $parent->getId()) {
                return true;
            }
        } while ($parent);

        return false;
    }

}
