<?php

namespace Tests\module\Assignment\Model\Log;

use PHPUnit\Framework\TestCase;
use module\Assignment\Model\Log\Role;

class RoleLog extends TestCase {

    public function testAdd() {
        //input
        $id = new \MongoId();
        $token = "123";
        $app_id = "123";
        $role_id = "123";
        $creator_id = "123";
        $metatype = "123";
        $message = "123";
        $create_at = new \DateTime();

        //expeted
        $documentexperted = new \module\Assignment\Model\Collection\RoleLog();
        $documentexperted->setId($id);
        $documentexperted->setToken($token);
        $documentexperted->setAppId($app_id);
        $documentexperted->setRoleId($role_id);
        $documentexperted->setCreatorId($creator_id);
        $documentexperted->setMetatype($metatype);
        $documentexperted->setMessage($message);
        $documentexperted->setCreateAt($create_at);
        $documentexperted->setUpdateAt($create_at);

        //mockup
        $configMock = [];

        $connectMock = $this->getMockBuilder('DocumentManager')
                ->setMethods(array('persist', 'flush'))
                ->disableOriginalConstructor()
                ->getMock();

        $connectMock->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($documentexperted));
        $connectMock->expects($this->once())
                ->method('flush');



        $codeMock = new \system\Helper\Code($configMock, $connectMock);

        $entityMock = new Role($connectMock, $codeMock, $configMock);

        //input
        $data = (object) [
                    "app_id"     => $app_id,
                    "creator_id" => $creator_id,
                    "role_id"    => $role_id,
                    "metatype"   => $metatype,
                    "message"    => $message,
                    "token"      => $token,
                    "id"         => $id,
                    "create_at"  => $create_at,
                    "update_at"  => $create_at,
        ];

        //test
        $entityMock->add($data);
    }

    public function testFind() {
        $foo = true;
        $this->assertTrue($foo);
    }

}
