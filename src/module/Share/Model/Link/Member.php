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

            $this->dm->persist($member);
            $this->dm->flush();

            //add new member log
            $memberlog = new \module\Share\Model\Log\Member($this->dm, $this->code, $this->config);

            $memberlog->add((object) [
                        'user_id' => (string) $user->getId(),
                        'app_id' => (string) $app->getId(),
                        "metatype" => "add",
                        'message' => "<a href='{$this->config['URL_ROOT']}/application/user/view/{$user->getId()}'>{$user->getName()}</a> đã được thêm vào ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a>"
            ]);


            return $member;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function update($id, $data) {
        
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

        $memberlog = new \module\Share\Model\Log\Member($this->dm, $this->code, $this->config);

        $memberlog->add((object) [
                    'user_id' => (string) $user->getId(),
                    'app_id' => (string) $app->getId(),
                    "metatype" => "remove",
                    'message' => "Thành viên <a href='{$this->config['URL_ROOT']}/application/user/view/{$user->getId()}'>{$user->getName()}</a> đã được loại bỏ khỏi ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a>"
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

        $memberlog = new \module\Share\Model\Log\Member($this->dm, $this->code, $this->config);

        $memberlog->add((object) [
                    'user_id' => (string) $user->getId(),
                    'app_id' => (string) $app->getId(),
                    "metatype" => "restore",
                    'message' => "Thành viên <a href='{$this->config['URL_ROOT']}/application/user/view/{$user->getId()}'>{$user->getName()}</a> đã được khôi phục lại trong ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a>"
        ]);

        $this->getCode()->success("restore is successfully");
    }

    public function find($id, $type = '') {
        //find by id
        return $this->dm->getRepository(\module\Share\Model\Collection\Member::class)->find($id);
    }


}
