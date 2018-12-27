<?php

namespace module\Share\Model\Entity;

class App extends \module\Share\Model\Common\AbsEntity {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

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
        //check existed
        if ($this->find($data->metatype, 'metatype')) {
            $this->code->forbidden("metatype is existed in system");
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

            return $app;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function find($id, $type = '') {
        switch ($type) {
            case 'metatype':
                return $this->dm->getRepository(\module\Share\Model\Collection\App::class)->findOneBy(['metatype' => $id]);
                break;
            case 'domain':
                return $this->dm->getRepository(\module\Share\Model\Collection\App::class)->findOneBy(['domain' => $id]);
                break;
            default :
                //find by id
                $find = $this->dm->getRepository(\module\Share\Model\Collection\App::class)->find($id);

                //find by metatype
                if (!$find) {

                    $find = $this->dm->getRepository(\module\Share\Model\Collection\App::class)->findOneBy(['metatype' => $id]);
                }

                //find by domain
                if (!$find) {
                    $find = $this->dm->getRepository(\module\Share\Model\Collection\App::class)->findOneBy(['domain' => $id]);
                }

                return $find;
                break;
        }
    }

}
