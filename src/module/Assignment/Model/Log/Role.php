<?php

namespace module\Assignment\Model\Log;

class Role extends \module\Share\Model\Common\AbsLink {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    public function __construct($connect, \system\Helper\Code $code, $config) {
        $this->init($connect, $code, $config);
    }

    public function add($data) {
        //input app id 
        if (\system\Helper\Validate::isEmpty($data->app_id)) {
            $this->code->forbidden("app_id is require");
        }

        //input creator id 
        if (\system\Helper\Validate::isEmpty($data->creator_id)) {
            $this->code->forbidden("creator_id is require");
        }

        //input role id 
        if (\system\Helper\Validate::isEmpty($data->role_id)) {
            $this->code->forbidden("role_id is require");
        }

        if (!\system\Helper\Validate::isString($data->role_id)) {
            $this->code->forbidden("type input role_id is not string");
        }

        //input metatype
        if (\system\Helper\Validate::isEmpty($data->metatype)) {
            $this->code->forbidden("metatype is require");
        }

        if (!\system\Helper\Validate::isString($data->metatype)) {
            $this->code->forbidden("type input metatype is not string");
        }

        //input message
        if (\system\Helper\Validate::isEmpty($data->message)) {
            $this->code->forbidden("message is require");
        }

        if (!\system\Helper\Validate::isString($data->message)) {
            $this->code->forbidden("type input message is not string");
        }


        try {

            //add new log
            $log = new \module\Assignment\Model\Collection\RoleLog($data->role_id, $data->metatype, $data->message, $data->app_id, $data->creator_id);

            $this->dm->persist($log);
            $this->dm->flush();

            return $log;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }

    public function update($id, $data) {
        
    }

    public function remove($id) {
        
    }

    public function restore($id) {
        
    }

    public function find($id, $type = '') {
        //find by id
        return $this->dm->getRepository(\module\Assignment\Model\Collection\RoleLog::class)->find($id);
    }

}
