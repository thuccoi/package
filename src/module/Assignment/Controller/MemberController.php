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

        $this->toParamJs('appid', $this->getViewer()->app->id);

        //data json
        $this->toParamJs('dataJSON', \system\Helper\ArrayCallback::select($members, function($e) {
                    return ["id" => $e->getId(), "parentid" => ($e->getManager() ? $e->getManager()->getId() : ''), 'name' => $e->User()->getName()];
                }));

        return [
            "roles"   => $roles,
            "members" => $members
        ];
    }

    public function editFormAction() {

        $id = $this->getRouter()->getId();

        $obj = $this->entity_member->find($id);

        if (!$obj || $obj->getApp()->getId() != $this->getViewer()->app->id) {
            $this->getCode()->notfound("Không tìm thấy thành viên này trong hệ thống.");
        }

        $members = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\Member::class)
                ->field('id')->notEqual($id)
                ->field('app.id')->equals($this->getViewer()->app->id)
                ->sort('create_at', 'desc')
                ->getQuery()
                ->execute();

        $roles = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\Role::class)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->getQuery()
                ->execute();
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/View/');

        $this->toParamJs('memberid', $id);

        return [
            "member"  => $obj,
            "roles"   => $roles,
            "members" => $members
        ];
    }

    public function editAction() {

        //get id on router
        $id = $this->getRouter()->getId('id');
        //get data
        $data = (object) [
                    "alias"       => $this->getCode()->post("alias"),
                    "title"       => $this->getCode()->post("title"),
                    "description" => $this->getCode()->post("description"),
                    "manager"     => $this->getCode()->post("manager"),
                    "role"        => $this->getCode()->arr("role", "POST")
        ];

        $data->app_id = $this->getViewer()->app->id;
        $data->creator_id = $this->getViewer()->member->id;

        //update the obj
        $obj = $this->entity_member->update($id, $data);

        //check redirect
        if ($this->getCode()->post('fromajax')) {

            $this->getCode()->success("Thành viên đã được chỉnh sửa thành công.");
        }

        $this->getCode()->success("Thành viên đã được chỉnh sửa thành công.", [], $this->url('assignment', ['controller' => 'member']));
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

    public function indexLogAction() {
        $id = $this->getRouter()->getId();

        $app = $this->getConnect()->getRepository(\module\Share\Model\Collection\App::class)->find($id);
        if (!$app) {
            $this->getCode()->error("Not found app");
        }

        $start = (int) $this->getCode()->post('start');

        //get length load more in config
        $lenghtloadmore = (int) $this->getConfig()['render']['lenghtloadmore'];

        //list add logs 
        $qb = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleToMemberLog::class)
                ->hydrate(false)
                ->field("app_id")->equals($app->getId())
                ->sort('create_at', 'desc')
                ->skip($start * $lenghtloadmore)
                ->limit($lenghtloadmore)
                ->getQuery()
                ->execute();

        $applogs = [];

        if ($qb) {
            foreach ($qb as $val) {
                $valrl = $val;
                $valrl['create_at'] = \system\Helper\Str::toTimeString($val["create_at"]->toDateTime());
                $applogs[] = $valrl;
            }
        }

        //numlogs
        $numlogs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleToMemberLog::class)
                ->hydrate(false)
                ->field("app_id")->equals($app->getId())
                ->count()
                ->getQuery()
                ->execute();

        //hide button load more
        $hideloadmore = 0;
        if (count($applogs) < $lenghtloadmore) {//less $lenghtloadmore documents
            $hideloadmore = 1;
        } else if ($numlogs == $start * $lenghtloadmore + $lenghtloadmore) {//or end of logs
            $hideloadmore = 1;
        }

        $this->getCode()->success("Lịch sử của ứng dụng {$app->getName()}", ['logs' => $applogs, 'hideloadmore' => $hideloadmore]);
    }

    public function editLogAction() {
        $id = $this->getRouter()->getId();

        $member = $this->getConnect()->getRepository(\module\Share\Model\Collection\Member::class)->find($id);
        if (!$member) {
            $this->getCode()->error("Not found member");
        }

        $start = (int) $this->getCode()->post('start');

        //get length load more in config
        $lenghtloadmore = (int) $this->getConfig()['render']['lenghtloadmore'];

        //list add logs 
        $qb = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\MemberLog::class)
                ->hydrate(false)
                ->field("user_id")->equals($member->getUser()->getId())
                ->field("app_id")->equals($member->getApp()->getId())
                ->sort('create_at', 'desc')
                ->skip($start * $lenghtloadmore)
                ->limit($lenghtloadmore)
                ->getQuery()
                ->execute();

        $logs = [];

        if ($qb) {
            foreach ($qb as $val) {
                $valrl = $val;
                $valrl['create_at'] = \system\Helper\Str::toTimeString($val["create_at"]->toDateTime());
                $logs[] = $valrl;
            }
        }

        //numlogs
        $numlogs = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\MemberLog::class)
                ->hydrate(false)
                ->field("user_id")->equals($member->getUser()->getId())
                ->field("app_id")->equals($member->getApp()->getId())
                ->count()
                ->getQuery()
                ->execute();

        //hide button load more
        $hideloadmore = 0;
        if (count($logs) < $lenghtloadmore) {//less $lenghtloadmore documents
            $hideloadmore = 1;
        } else if ($numlogs == $start * $lenghtloadmore + $lenghtloadmore) {//or end of logs
            $hideloadmore = 1;
        }

        $this->getCode()->success("Lịch sử của thành viên {$member->getUser()->getName()}", ['logs' => $logs, 'hideloadmore' => $hideloadmore]);
    }

}
