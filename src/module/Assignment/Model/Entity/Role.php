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
        //name
        if (\system\Helper\Validate::isEmpty($data->name)) {
            $this->code->forbidden("name is require");
        }

        if (!\system\Helper\Validate::isString($data->name)) {
            $this->code->forbidden("name was not string");
        }

        //metatype
        if (\system\Helper\Validate::isEmpty($data->metatype)) {
            $this->code->forbidden("metatype is require");
        }

        if (!\system\Helper\Validate::isString($data->metatype)) {
            $this->code->forbidden("metatype was not string");
        }


        //check existed
        if ($this->find($data->metatype, 'metatype')) {
            $this->code->forbidden("metatype is existed in system");
        }

        try {
            //new obj
            $obj = new \module\Assignment\Model\Collection\Role();

            //set information
            $obj->setName($data->name)
                    ->setMetatype($data->metatype);

            //save and send email
            $this->dm->persist($obj);
            $this->dm->flush();

            //log create app
//            $log = new \module\Assignment\Model\Log\Role($this->dm, $this->code, $this->config);
//            $log->add((object) [
//                        "role_id"  => (string) $obj->getId(),
//                        "metatype" => "create",
//                        "message"  => "Vai trò <a href='{$this->config['URL_ROOT']}/assignment/role/view/{$obj->getId()}'>{$obj->getName()}</a> đã được tạo mới"
//            ]);

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
