<?php

namespace module\Assignment\Controller;

class RoleController extends \system\Template\AbstractController {

    //entity user
    protected $entity;
    protected $entity_pertorole;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Assignment\Model\Entity\Role($connect, $code, $config, $session);
        $this->entity_pertorole = new \module\Assignment\Model\Link\PermissionToRole($connect, $code, $config, $session);
    }

    public function indexAction() {
        $objs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\Role::class)
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

        //data json
        $this->toParamJs('dataJSON', \system\Helper\ArrayCallback::select($objs, function($e) {
                    return ["id" => $e->getId(), "parentid" => ($e->getParent() ? $e->getParent()->getId() : ''), 'name' => $e->getName()];
                }));

        $this->toParamJs('appid', $this->getViewer()->app->id);

        return [
            "roles"   => $objs,
            "members" => $members
        ];
    }

    public function hasPermissionAction() {

        $permission = $this->getCode()->post('permission');
        $roleid = $this->getCode()->post('role_id');

        $count = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\PermissionToRole::class)
                ->field('permission')->equals($permission)
                ->field('role.id')->equals($roleid)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->count()
                ->getQuery()
                ->execute();

        $this->getCode()->success("ok", ["status" => ($count > 0)]);
    }

    public function removePermissionAction() {

        $data = (object) [
                    "permission" => $this->getCode()->post('permission'),
                    "role_id"    => $this->getCode()->post('role_id')
        ];

        $data->app_id = $this->getViewer()->app->id;
        $data->creator_id = $this->getViewer()->member->id;

        $this->entity_pertorole->remove($data);

        $this->getCode()->success("ok");
    }

    public function addPermissionAction() {

        $data = (object) [
                    "permission" => $this->getCode()->post('permission'),
                    "role_id"    => $this->getCode()->post('role_id')
        ];

        $data->app_id = $this->getViewer()->app->id;
        $data->creator_id = $this->getViewer()->member->id;

        $this->entity_pertorole->add($data);

        $this->getCode()->success("ok");
    }

    public function createFormAction() {

        $objs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\Role::class)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->getQuery()
                ->execute();

        //view dir
        $this->setViewDir(dirname(__DIR__) . '/View/');

        //data json
        $this->toParamJs('dataJSON', \system\Helper\ArrayCallback::select($objs, function($e) {
                    return ["id" => $e->getId(), "parentid" => ($e->getParent() ? $e->getParent()->getId() : ''), 'name' => $e->getName()];
                }));

        $this->toParamJs('appid', $this->getViewer()->app->id);

        return [
            "roles" => $objs
        ];
    }

    public function createAction() {

        //get data
        $data = (object) [
                    "name"        => $this->getCode()->post("name"),
                    "description" => $this->getCode()->post("description"),
                    "parent"      => $this->getCode()->post("parent"),
                    "permission"  => $this->getCode()->arr("permission", "POST")
        ];

        //viewer
        $data->app_id = $this->getViewer()->app->id;
        $data->creator_id = $this->getViewer()->member->id;

        //create new obj
        $obj = $this->entity->create($data);

        if ($this->getCode()->post('fromajax')) {
            $this->getCode()->success("Vai trò đã được tạo thành công.");
        }

        $this->getCode()->success("Vai trò đã được tạo thành công.", [], $this->url('assignment', ['controller' => 'role']));
    }

    public function editFormAction() {

        $id = $this->getRouter()->getId();

        $obj = $this->entity->find($id);

        if (!$obj || $obj->getAppId() != $this->getViewer()->app->id) {
            $this->getCode()->notfound("Không tìm thấy vai trò này trong hệ thống.");
        }


        $objs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\Role::class)
                ->field('app_id')->equals($this->getViewer()->app->id)
                ->getQuery()
                ->execute();

        //view dir
        $this->setViewDir(dirname(__DIR__) . '/View/');

        //data json
        $this->toParamJs('dataJSON', \system\Helper\ArrayCallback::select($objs, function($e) {
                    return ["id" => $e->getId(), "parentid" => ($e->getParent() ? $e->getParent()->getId() : ''), 'name' => $e->getName()];
                }));


        $this->toParamJs('roleid', $obj->getId());

        return [
            "role"  => $obj,
            "roles" => $objs
        ];
    }

    public function editAction() {


        //get id on router
        $id = $this->getRouter()->getId('id');
        //get data
        $data = (object) [
                    "name"        => $this->getCode()->post("name"),
                    "description" => $this->getCode()->post("description"),
                    "parent"      => $this->getCode()->post("parent"),
                    "permission"  => $this->getCode()->arr("permission", "POST")
        ];

        //viewer
        $data->app_id = $this->getViewer()->app->id;
        $data->creator_id = $this->getViewer()->member->id;

        //edit the obj
        $obj = $this->entity->edit($id, $data);

        //check redirect
        if ($this->getCode()->post('fromajax')) {

            $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.");
        }

        $this->getCode()->success("Vai trò đã được chỉnh sửa thành công.", [], $this->url('assignment', ['controller' => 'role']));
    }

    public function deleteAction() {
        
    }

    public function restoreAction() {
        
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
        $qb = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleLog::class)
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
        $numlogs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleLog::class)
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

    public function permissionLogAction() {
        $id = $this->getRouter()->getId();

        $app = $this->getConnect()->getRepository(\module\Share\Model\Collection\App::class)->find($id);
        if (!$app) {
            $this->getCode()->error("Not found app");
        }

        $start = (int) $this->getCode()->post('start');

        //get length load more in config
        $lenghtloadmore = (int) $this->getConfig()['render']['lenghtloadmore'];

        //list add logs 
        $qb = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\PermissionToRoleLog::class)
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
        $numlogs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\PermissionToRoleLog::class)
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

    public function createLogAction() {
        $id = $this->getRouter()->getId();

        $app = $this->getConnect()->getRepository(\module\Share\Model\Collection\App::class)->find($id);
        if (!$app) {
            $this->getCode()->error("Not found app");
        }

        $start = (int) $this->getCode()->post('start');

        //get length load more in config
        $lenghtloadmore = (int) $this->getConfig()['render']['lenghtloadmore'];

        //list add logs 
        $qb = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleLog::class)
                ->hydrate(false)
                ->field("app_id")->equals($app->getId())
                ->field('metatype')->equals('create')
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
        $numlogs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleLog::class)
                ->hydrate(false)
                ->field("app_id")->equals($app->getId())
                ->field('metatype')->equals('create')
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

        $obj = $this->entity->find($id);

        if (!$obj || $obj->getAppId() != $this->getViewer()->app->id) {
            $this->getCode()->notfound("Không tìm thấy vai trò này trong hệ thống.");
        }

        $start = (int) $this->getCode()->post('start');

        //get length load more in config
        $lenghtloadmore = (int) $this->getConfig()['render']['lenghtloadmore'];

        //list add logs 
        $qb = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleLog::class)
                ->hydrate(false)
                ->field("role_id")->equals($obj->getId())
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
        $numlogs = $this->getConnect()->createQueryBuilder(\module\Assignment\Model\Collection\RoleLog::class)
                ->hydrate(false)
                ->field("role_id")->equals($obj->getId())
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

        $this->getCode()->success("Lịch sử của vai trò {$obj->getName()}", ['logs' => $logs, 'hideloadmore' => $hideloadmore]);
    }

}
