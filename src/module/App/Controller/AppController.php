<?php

namespace module\App\Controller;

class AppController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\App($connect, $code, $config, $session);
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
                    "name"       => $this->getCode()->post("name"),
                    "onboarding" => $this->getCode()->post("onboarding"),
                    "image"      => $this->getCode()->post("image"),
                    "metatype"   => $this->getCode()->post("metatype"),
                    "domain"     => $this->getCode()->post("domain")
        ];

        //edit the app
        $app = $this->entity->edit($id, $data);

        //check redirect
        if ($this->getCode()->post('fromajax')) {

            $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.");
        }

        $this->getCode()->success("Ứng dụng đã được chỉnh sửa thành công.", []);
    }

    public function deleteAction() {
        
    }

    public function restoreAction() {
        
    }

    
    
    
    public function memberLogAction() {

        $id = $this->getRouter()->getId();

        $app = $this->getConnect()->getRepository(\module\Share\Model\Collection\App::class)->find($id);
        if (!$app) {
            $this->getCode()->error("Not found app");
        }

        //get length load more in config
        $lenghtloadmore = (int) $this->getConfig()['render']['lenghtloadmore'];


        $start = (int) $this->getCode()->post('start');

        //list member logs
        $qb = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\MemberLog::class)
                ->hydrate(false)
                ->field("app_id")->equals($app->getId())
                ->sort('create_at', 'desc')
                ->skip($start * $lenghtloadmore)
                ->limit($lenghtloadmore)
                ->getQuery()
                ->execute();

        $memberlogs = [];

        if ($qb) {
            foreach ($qb as $val) {
                $valrl = $val;
                $valrl['create_at'] = \system\Helper\Str::toTimeString($val["create_at"]->toDateTime());
                $memberlogs[] = $valrl;
            }
        }

        //numlogs
        $numlogs = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\MemberLog::class)
                ->hydrate(false)
                ->field("app_id")->equals($app->getId())
                ->count()
                ->getQuery()
                ->execute();

        //hide button load more
        $hideloadmore = 0;
        if (count($memberlogs) < $lenghtloadmore) {//less $lenghtloadmore documents
            $hideloadmore = 1;
        } else if ($numlogs == $start * $lenghtloadmore + $lenghtloadmore) {//or end of logs
            $hideloadmore = 1;
        }

        $this->getCode()->success("Lịch sử các thành viên trong ứng dụng {$app->getName()}", ['logs' => $memberlogs, 'hideloadmore' => $hideloadmore]);
    }

    public function appLogAction() {
        $id = $this->getRouter()->getId();

        $app = $this->getConnect()->getRepository(\module\Share\Model\Collection\App::class)->find($id);
        if (!$app) {
            $this->getCode()->error("Not found app");
        }

        $start = (int) $this->getCode()->post('start');

        //get length load more in config
        $lenghtloadmore = (int) $this->getConfig()['render']['lenghtloadmore'];

        //list add logs 
        $qb = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\AppLog::class)
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
        $numlogs = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\AppLog::class)
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
}
