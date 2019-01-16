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
        if ($this->getViewer()->role != \module\Share\Model\Collection\Member::ROLE_OWNER) {
            $this->getCode()->forbidden("You don't permission to create a new application.");
        }

        //get data
        $data = (object) [
                    "name" => $this->getCode()->post("name"),
                    "image" => $this->getCode()->post("image"),
                    "metatype" => $this->getCode()->post("metatype"),
                    "domain" => $this->getCode()->post("domain")
        ];

        //create new app
        $app = $this->entity->create($data);
        if ($this->getCode()->post('fromajax')) {
            $this->getCode()->success("Ứng dụng đã được tạo thành công.");
        }

        $this->getCode()->success("Ứng dụng đã được tạo thành công.", [], $this->url('application', ['controller' => 'index', 'action' => 'view', 'id' => $app->getId()]));
    }

    public function editAction() {
        if ($this->getViewer()->role != \module\Share\Model\Collection\Member::ROLE_OWNER) {
            $this->getCode()->forbidden("You don't permission to edit this application.");
        }

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
        $app = $this->entity->edit($id, $data);

        //check redirect
        if ($this->getCode()->post('fromajax')) {

            $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.");
        }

        $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.", [], $this->url('application', ['controller' => 'index', 'action' => 'view', 'id' => $app->getId()]));
    }

    public function deleteAction() {
        if ($this->getViewer()->role != \module\Share\Model\Collection\Member::ROLE_OWNER) {
            $this->getCode()->forbidden("You don't permission to delete this application.");
        }
    }

    public function restoreAction() {
        if ($this->getViewer()->role != \module\Share\Model\Collection\Member::ROLE_OWNER) {
            $this->getCode()->forbidden("You don't permission to restore this application.");
        }
    }

}
