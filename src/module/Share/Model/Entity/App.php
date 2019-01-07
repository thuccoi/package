<?php

namespace module\Share\Model\Entity;

class App extends \module\Share\Model\Common\AbsEntity {

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

        //image
        if (\system\Helper\Validate::isEmpty($data->image)) {
            $this->code->forbidden("image is require");
        }

        if (!\system\Helper\Validate::isString($data->image)) {
            $this->code->forbidden("image was not string");
        }

        //metatype
        if (\system\Helper\Validate::isEmpty($data->metatype)) {
            $this->code->forbidden("metatype is require");
        }

        if (!\system\Helper\Validate::isString($data->metatype)) {
            $this->code->forbidden("metatype was not string");
        }

        //domain
        if (\system\Helper\Validate::isEmpty($data->domain)) {
            $this->code->forbidden("domain is require");
        }

        if (!\system\Helper\Validate::isString($data->domain)) {
            $this->code->forbidden("domain was not string");
        }

        //check existed
        if ($this->find($data->domain, 'domain')) {
            $this->code->forbidden("domain is existed in system");
        }



        try {
            //new app
            $app = new \module\Share\Model\Collection\App();

            //set information
            $app
                    ->setName($data->name)
                    ->setImage($data->image)
                    ->setMetatype($data->metatype)
                    ->setDomain($data->domain);

            //save and send email
            $this->dm->persist($app);
            $this->dm->flush();

            //log create app
            $applog = new \module\Share\Model\Log\App($this->dm, $this->code, $this->config);
            $applog->add((object) [
                        "app_id" => (string) $app->getId(),
                        "message" => "Ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a> đã được tạo mới"
            ]);

            return $app;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function edit($id, $data) {
        $app = $this->find($id);
        if ($app) {
            $editinfo = [];
            //edit name
            if (!\system\Helper\Validate::isEmpty($data->name) && $data->name != $app->getName()) {
                $app->setName($data->name);
                $editinfo [] = "Tên của ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a> đã được đổi thành {$data->name }";
            }

            //edit image
            if (!\system\Helper\Validate::isEmpty($data->image) && $data->image != $app->getImage()) {
                $app->setImage($data->image);
                $editinfo [] = "Ảnh của ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a> đã được đổi thành <img src='{$data->image}'>";
            }

            //edit metatype
            if (!\system\Helper\Validate::isEmpty($data->metatype) && $data->metatype != $app->getMetatype()) {
                $app->setMetatype($data->metatype);
                $editinfo [] = "Loại của ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a> đã được đổi thành {$data->metatype}>";
            }

            //edit domain
            if (!\system\Helper\Validate::isEmpty($data->domain) && $data->domain != $app->getDomain()) {
                $app->setDomain($data->domain);
                $editinfo [] = "Tên miền của ứng dụng <a href='{$this->config['URL_ROOT']}/application/index/view/{$app->getId()}'>{$app->getName()}</a> đã được đổi thành {$data->domain}>";
            }

            if (\system\Helper\Validate::isEmpty($editinfo)) {
                $this->code->error("Data not changed");
            }

            //save and send email
            $this->dm->persist($app);
            $this->dm->flush();

            //log create app
            foreach ($editinfo as $message) {
                $applog = new \module\Share\Model\Log\App($this->dm, $this->code, $this->config);
                $applog->add((object) [
                            "app_id" => (string) $app->getId(),
                            "message" => $message
                ]);
            }
            return $app;
        } else {
            $this->code->notfound("App not exists in system");
        }
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
            case 'domain':
                return $this->dm->getRepository(\module\Share\Model\Collection\App::class)->findOneBy(['domain' => $id]);
                break;
            default :
                //find by id
                $find = $this->dm->getRepository(\module\Share\Model\Collection\App::class)->find($id);

                //find by domain
                if (!$find) {
                    $find = $this->dm->getRepository(\module\Share\Model\Collection\App::class)->findOneBy(['domain' => $id]);
                }

                return $find;
                break;
        }
    }

}
