<?php

namespace module\Assignment\Controller;

class RoleController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Assignment\Model\Entity\Role($connect, $code, $config);
    }

    public function indexAction() {
        $objs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\Role::class)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->getQuery()
                ->execute();

        //view dir
        $this->setViewDir(dirname(__DIR__) . '/View/');

        return [
            "roles" => $objs
        ];
    }

    public function createAction() {
        if ($this->getViewer()->role != \module\Share\Model\Collection\Member::ROLE_OWNER) {
            $this->getCode()->forbidden("You don't permission to create a new role.");
        }

        //get data
        $data = (object) [
                    "name"        => $this->getCode()->post("name"),
                    "children_id" => $this->getCode()->post("children_id")
        ];

        //viewer
        $data->viewer = $this->getViewer();

        //create new obj
        $obj = $this->entity->create($data);
        if ($this->getCode()->post('fromajax')) {
            $this->getCode()->success("Vai trò đã được tạo thành công.");
        }

        $this->getCode()->success("Vai trò đã được tạo thành công.", [], $this->url('assignment', ['controller' => 'role', 'action' => 'view', 'id' => $obj->getId()]));
    }

    public function editAction() {
        if ($this->getViewer()->role != \module\Share\Model\Collection\Member::ROLE_OWNER) {
            $this->getCode()->forbidden("You don't permission to edit this role.");
        }

        //get id on router
        $id = $this->getRouter()->getId('id');
        //get data
        $data = (object) [
                    "name" => $this->getCode()->post("name")
        ];

        //edit the obj
        $obj = $this->entity->edit($id, $data);

        //check redirect
        if ($this->getCode()->post('fromajax')) {

            $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.");
        }

        $this->getCode()->success("Vai trò đã được chỉnh sửa thành công.", [], $this->url('assignment', ['controller' => 'role', 'action' => 'view', 'id' => $obj->getId()]));
    }

    public function deleteAction() {
        if ($this->getViewer()->role != \module\Share\Model\Collection\Member::ROLE_OWNER) {
            $this->getCode()->forbidden("You don't permission to delete this role.");
        }
    }

    public function restoreAction() {
        if ($this->getViewer()->role != \module\Share\Model\Collection\Member::ROLE_OWNER) {
            $this->getCode()->forbidden("You don't permission to restore this role.");
        }
    }

}
