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

        $id = $this->getViewer()->app->id;

        $app = $this->getConnect()->getRepository(\module\Share\Model\Collection\App::class)->find($id);
        if (!$app) {
            $this->getCode()->error("Not found app");
        }

        //member
        $members = [];
        if ($app->getMembers()) {
            foreach ($app->getMembers() as $val) {
                $member = $val->release();
                $member->user = $val->getUser()->release();
                $members [] = $member;
            }
        }

        //memde delete
        $memdels = [];
        if ($app->getMembersWD()) {
            foreach ($app->getMembersWD() as $val) {
                $member = $val->release();
                $member->user = $val->getUser()->release();
                $memdels [] = $member;
            }
        }

        $memberlogs = [];

        $applogs = [];


        //view dir
        $this->setViewDir(dirname(__DIR__) . '/View/');

        //to js
        $this->toParamJs("appid", (string) $app->getId());
        $this->toParamJs('members', $members);
        $this->toParamJs('memdels', $memdels);

        return [
            'app'        => $app,
            'members'    => $members,
            'memberlogs' => $memberlogs,
            'applogs'    => $applogs,
            'memdels'    => $memdels
        ];
    }

    public function createAction() {


        //get data
        $data = (object) [
                    "name"     => $this->getCode()->post("name"),
                    "image"    => $this->getCode()->post("image"),
                    "metatype" => $this->getCode()->post("metatype"),
                    "domain"   => $this->getCode()->post("domain")
        ];

        //create new app
        $app = $this->entity->create($data);
        if ($this->getCode()->post('fromajax')) {
            $this->getCode()->success("Ứng dụng đã được tạo thành công.");
        }

        $this->getCode()->success("Ứng dụng đã được tạo thành công.", [], $this->url('application', ['controller' => 'index', 'action' => 'view', 'id' => $app->getId()]));
    }

    public function editAction() {


        //get id on router
        $id = $this->getRouter()->getId('id');
        //get data
        $data = (object) [
                    "name"     => $this->getCode()->post("name"),
                    "image"    => $this->getCode()->post("image"),
                    "metatype" => $this->getCode()->post("metatype"),
                    "domain"   => $this->getCode()->post("domain")
        ];

        //edit the app
        $app = $this->entity->edit($id, $data);

        //check redirect
        if ($this->getCode()->post('fromajax')) {

            $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.");
        }

        $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.", [], '#');
    }

    public function deleteAction() {
        
    }

    public function restoreAction() {
        
    }

}
