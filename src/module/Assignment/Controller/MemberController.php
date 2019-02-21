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

        $memid = $this->getCode()->post('member_id');
        $roleid = $this->getCode()->post('role_id');

        $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleToMember::class)
                ->field('member.id')->equals($memid)
                ->field('role.id')->equals($roleid)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->remove()
                ->getQuery()
                ->execute();

        $this->getCode()->success("ok");
    }

    public function addRoleAction() {

        $memid = $this->getCode()->post('member_id');
        $roleid = $this->getCode()->post('role_id');

        $count = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleToMember::class)
                ->field('member.id')->equals($memid)
                ->field('role.id')->equals($roleid)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->count()
                ->getQuery()
                ->execute();

        if ($count > 0) {
            $this->getCode()->forbidden("Thành viên đã có vai trò này rồi");
        }

        $role = $this->entity->find($roleid);
        if (!$role) {
            $this->getCode()->notfound("Không tìm thấy vai trò này trong hệ thống");
        }

        $member = $this->entity_member->find($memid);
        if (!$member) {
            $this->getCode()->notfound("Không tìm thấy thành viên này trong hệ thống");
        }

        $obj = new \module\Assignment\Model\Collection\RoleToMember();
        $obj->setAppId($this->getViewer()->app->id);
        $obj->setMember($member);
        $obj->setRole($role);

        $this->getConnect()->persist($obj);
        $this->getConnect()->flush();

        $this->getCode()->success("ok");
    }

}
