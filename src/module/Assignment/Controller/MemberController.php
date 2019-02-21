<?php

namespace module\Assignment\Controller;

class MemberController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Assignment\Model\Entity\Role($connect, $code, $config);
        $this->entity_member = new \module\Share\Model\Link\Member($connect, $code, $config);
        $this->entity_roletomember = new \module\Assignment\Model\Link\RoleToMember($connect, $code, $config);
    }

    public function indexAction() {


        $roles = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\Role::class)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->getQuery()
                ->execute();

        $members = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\Member::class)
                ->field('app.id')->equals($this->getViewer()->app->id)
                ->sort('create_at', 'desc')
                ->getQuery()
                ->execute();


        //view dir
        $this->setViewDir(dirname(__DIR__) . '/View/');

        return [
            "roles"   => $roles,
            "members" => $members
        ];
    }

    public function hasRoleAction() {

        $memid = $this->getCode()->post('member_id');
        $roleid = $this->getCode()->post('role_id');

        $count = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleToMember::class)
                ->field('member.id')->equals($memid)
                ->field('role.id')->equals($roleid)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->count()
                ->getQuery()
                ->execute();

        $this->getCode()->success("ok", ["status" => ($count > 0)]);
    }

    public function removeRoleAction() {

        $data = (object) [
                    "member_id" => $this->getCode()->post('member_id'),
                    "role_id"   => $this->getCode()->post('role_id')
        ];

        $data->app_id = $this->getViewer()->app->id;
        $data->creator_id = $this->getViewer()->member->id;

        $this->entity_roletomember->remove($data);

        $this->getCode()->success("ok");
    }

    public function addRoleAction() {

        $data = (object) [
                    "member_id" => $this->getCode()->post('member_id'),
                    "role_id"   => $this->getCode()->post('role_id')
        ];

        $data->app_id = $this->getViewer()->app->id;
        $data->creator_id = $this->getViewer()->member->id;

        $this->entity_roletomember->add($data);

        $this->getCode()->success("ok");
    }

}
