<?php

namespace module\App\Controller;

class AppController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\App($connect, $code, $config);
    }

    public function indexAction() {
        //layout
        $this->setLayout('TAMI_NOLAYOUT');
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/../Share/View/');
    }

    public function createAction() {
        //get data
        $data = (object) [
                    "name" => $this->getCode()->post("name"),
                    "image" => $this->getCode()->post("image"),
                    "metatype" => $this->getCode()->post("metatype"),
                    "domain" => $this->getCode()->post("domain")
        ];

        //create new app
        $this->entity->create($data);
        $this->getCode()->success("Ứng dụng đã được tạo thành công.");
    }

    public function editAction() {
        //get id on router
        $id = $this->getRouter()->getId('id');
        //get data
        $data = (object) [
                    "name" => $this->getCode()->post("name"),
                    "image" => $this->getCode()->post("image"),
                    "metatype" => $this->getCode()->post("metatype"),
                    "domain" => $this->getCode()->post("domain")
        ];

        //edit the app
        $this->entity->edit($id, $data);

        $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.");
    }

    public function removeAction() {
        
    }

}
