<?php

namespace module\Assignment\Model\Entity;

class Role extends \module\Share\Model\Common\AbsEntity {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    //set properties code
    public function __construct($connect, \system\Helper\Code $code, $config) {

        // $dm is a DocumentManager instance we should already have
        $this->init($connect, $code, $config);
    }

    public function create($data) {

        //field required
        //viewer
        if (\system\Helper\Validate::isEmpty($data->viewer)) {
            $this->code->forbidden("viewer is require");
        }

        if (!\system\Helper\Validate::isViewer($data->viewer)) {
            $this->code->forbidden("viewer invalid");
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
                    ->setAppId($data->viewer->app->id)
                    ->setCreatorId($data->viewer->member->id);

            //description
            if (\system\Helper\Validate::isString($data->description)) {
                $obj->setDescription($data->description);
            }

            //save 
            $this->dm->persist($obj);
            $this->dm->flush();

            //log create app
//            $log = new \module\Assignment\Model\Log\Role($this->dm, $this->code, $this->config);
//            $log->add((object) [
//                        "role_id"  => (string) $obj->getId(),
//                        "metatype" => "create",
//                        "message"  => "Vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được tạo mới"
//            ]);
            //add permission
            if (\system\Helper\Validate::isArray($data->permission)) {
                foreach ($data->permission as $val) {
                    $per = new \module\Assignment\Model\Collection\PermissionToRole();
                    $per->setPermission($val)
                            ->setRole($obj)
                            ->setAppId($data->viewer->app->id)
                            ->setCreatorId($data->viewer->member->id);

                    $this->dm->persist($per);
                    $this->dm->flush();
                }
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
                $obj->setName($data->name);
//                $editinfo [] = "<div class='timeline-content'>Tên của vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được đổi thành <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$data->name }</a></div>";
            }

            //edit description
            if (!\system\Helper\Validate::isEmpty($data->description) && $data->description != $obj->getDescription()) {
                $obj->setDescription($data->description);
//                $editinfo [] = "<div class='timeline-content'>Tên của vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được đổi thành <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$data->name }</a></div>";
            }

            //edit parent
            if (!\system\Helper\Validate::isEmpty($data->parent)) {
                if (($obj->getParent() && $obj->getParent()->getId() != $data->parent) || !$obj->getParent()) {
                    $parent = $this->find($data->parent);
                    if (!$parent) {
                        $this->code->notfound("parent is notfound in this application");
                    }
                    $obj->setParent($parent);
                }

//                $editinfo [] = "<div class='timeline-content'>Tên của vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được đổi thành <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$data->name }</a></div>";
            }

            //edit permission
            if (\system\Helper\Validate::isArray($data->permission)) {
                $oldper = $obj->getPermissions();
                //add new
                foreach ($data->permission as $val) {
                    if (!in_array($val, $oldper)) {
                        $per = new \module\Assignment\Model\Collection\PermissionToRole();
                        $per->setPermission($val)
                                ->setRole($obj)
                                ->setAppId($data->viewer->app->id)
                                ->setCreatorId($data->viewer->member->id);

                        $this->dm->persist($per);
                        $this->dm->flush();
                    }
                }

                //remove old
                foreach ($oldper as $val) {
                    if (!in_array($val, $data->permission)) {
                        $this->dm->createQueryBuilder(\module\Assignment\Model\Collection\PermissionToRole::class)
                                ->field('permission')->equals($val)
                                ->field('app_id')->equals($data->viewer->app->id)
                                ->remove()
                                ->getQuery()
                                ->execute()
                        ;
                    }
                }


//                 $editinfo [] = "<div class='timeline-content'>Tên của vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được đổi thành <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$data->name }</a></div>";
            }

            $this->dm->persist($obj);
            $this->dm->flush();

//            //log create app
//            foreach ($editinfo as $message) {
//                $applog = new \module\Assignment\Model\Log\Role($this->dm, $this->code, $this->config);
//                $applog->add((object) [
//                            "role_id"   => (string) $obj->getId(),
//                            "metatype" => "edit",
//                            "message"  => $message
//                ]);
//            }
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
        }
    }

    public function find($id, $type = '') {
        switch ($type) {
            case 'metatype':
                return $this->dm->getRepository(\module\Assignment\Model\Collection\Role::class)->findOneBy(['metatype' => $id]);
                break;
            default :
                //find by id
                $find = $this->dm->getRepository(\module\Assignment\Model\Collection\Role::class)->find($id);

                //find by metatype
                if (!$find) {
                    $find = $this->dm->getRepository(\module\Assignment\Model\Collection\Role::class)->findOneBy(['metatype' => $id]);
                }

                return $find;
                break;
        }

        return null;
    }

}
