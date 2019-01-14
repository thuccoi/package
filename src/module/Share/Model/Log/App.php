<?php

namespace module\Share\Model\Log;

class App extends \module\Share\Model\Common\AbsLog {

    //entity default
    use \module\Share\Model\Common\EntityDefault;

    public function __construct($connect, \system\Helper\Code $code, $config) {
        static::$parents = [];
        $this->init($connect, $code, $config);
    }

    public function add($data) {
        //input app id 
        if (\system\Helper\Validate::isEmpty($data->app_id)) {
            $this->code->forbidden("app_id is require");
        }

        if (!\system\Helper\Validate::isString($data->app_id)) {
            $this->code->forbidden("type input app_id is not string");
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

            //add new memeber log
            $memberlog = new \module\Share\Model\Collection\AppLog($data->app_id, $data->metatype, $data->message);

            $this->dm->persist($memberlog);
            $this->dm->flush();

            return $memberlog;
        } catch (\MongoException $ex) {
            throw $ex;
        }

        $this->code->error("Error database");
    }


}
