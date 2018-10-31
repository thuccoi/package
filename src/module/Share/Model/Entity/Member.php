<?php

namespace module\Share\Model\Entity;

class Member extends \module\Share\Model\Common\AbsEntity {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    private $application_entity;
    private $user_entity;

    public function __construct($connect, \system\Helper\Code $code, $config) {
        $this->init($connect, $code, $config);
        //init entity application
        $this->application_entity = new Application($connect, $code, $config);
        //init entity user
        $this->user_entity = new User($connect, $code, $config);
    }

    public function create($data) {

        //input application
        if (\system\Helper\Validate::isEmpty($data->application)) {
            $this->code->forbidden("application is require");
        }

        if (!\system\Helper\Validate::isString($data->application)) {
            $this->code->forbidden("type input application is not string");
        }

        $application = $this->application_entity->find($data->application);
        if (!$application) {
            $this->code->notfound("application notfound in system");
        }

        //input user
        if (\system\Helper\Validate::isEmpty($data->user)) {
            $this->code->forbidden("user is require");
        }

        if (!\system\Helper\Validate::isString($data->user)) {
            $this->code->forbidden("type input user is not string");
        }

        $user = $this->user_entity->find($data->user);
        if (!$user) {
            $this->code->notfound("user notfound in system");
        }

        //check member existed in system
        $check = $this->dm->createQueryBuilder(\module\Share\Model\Collection\Member::class)
                ->field('application.id')->equals($application->getId())
                ->field('user.id')->equals($user->getId())
                ->getQuery()
                ->execute();
        
        if ($check) {
            $this->code->forbidden("Member has existed in this Application");
        }
        
        try {
            //add new memeber
            $member = new \module\Share\Model\Collection\Member($application, $user);

            $this->dm->persist($member);
            $this->dm->flush();

            $this->code->success("Create new a member is successfuly");
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function find($id) {
        //find by id
        return $this->dm->getRepository(\module\Share\Model\Collection\Member::class)->find($id);
    }

}
