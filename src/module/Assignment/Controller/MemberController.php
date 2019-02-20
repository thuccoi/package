<?php

namespace module\Assignment\Controller;

class MemberController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Assignment\Model\Entity\Role($connect, $code, $config);
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

}
