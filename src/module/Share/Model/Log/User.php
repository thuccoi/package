<?php

namespace module\Share\Model\Log;

class User extends \module\Share\Model\Common\AbsLink {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    public function __construct($connect, \system\Helper\Code $code, $config) {
        $this->init($connect, $code, $config);
    }

    public function add($data) {

        //input user id
        if (\system\Helper\Validate::isEmpty($data->user_id)) {
            $this->code->forbidden("user_id is require");
        }

        if (!\system\Helper\Validate::isString($data->user_id)) {
            $this->code->forbidden("type input user_id is not string");
        }

        //input message
        if (\system\Helper\Validate::isEmpty($data->message)) {
            $this->code->forbidden("message is require");
        }

        if (!\system\Helper\Validate::isString($data->message)) {
            $this->code->forbidden("type input message is not string");
        }

        //input metatype
        if (\system\Helper\Validate::isEmpty($data->metatype)) {
            $this->code->forbidden("metatype is require");
        }

        if (!\system\Helper\Validate::isString($data->metatype)) {
            $this->code->forbidden("type input metatype is not string");
        }

        try {

            //add new  log
            $log = new \module\Share\Model\Collection\UserLog($data->user_id, $data->metatype, $data->message);

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
        return $this->dm->getRepository(\module\Share\Model\Collection\UserLog::class)->find($id);
    }

}
