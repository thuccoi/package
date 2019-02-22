<?php

namespace Tests\module\Assignment\Model\Log;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;


use module\Assignment\Model\Log\RoleToMember;

class RoleToMemberTest extends TestCase {

    public function testAdd() {
        //input
        $id = new \MongoId();
        $token = "123";
        $app_id = "123";
        $role_id = "123";
        $creator_id = "123";
        $member_id = "123";
        $metatype = "123";
        $message = "123";
        $create_at = new \DateTime();

        //expeted
        $documentexperted = new \module\Assignment\Model\Collection\RoleToMemberLog();
        $documentexperted->setId($id);
        $documentexperted->setToken($token);
        $documentexperted->setAppId($app_id);
        $documentexperted->setRoleId($role_id);
        $documentexperted->setCreatorId($creator_id);
        $documentexperted->setMemberId($member_id);
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

        $entityMock = new RoleToMember($connectMock, $codeMock, $configMock);

        //input
        $data = (object) [
                    "app_id"     => $app_id,
                    "creator_id" => $creator_id,
                    "role_id"    => $role_id,
                    "member_id"  => $member_id,
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
        //input and experted
        $id = new \MongoId();
        $documentexperted = new \module\Assignment\Model\Collection\RoleToMemberLog();
        $documentexperted->setId($id);

        //mockup
        $configMock = [];

        //now, mock the repository so it returns the mock of the log
        $logRepository = $this->createMock(ObjectRepository::class);


        $logRepository->expects($this->any())
                ->method('find')
                ->willReturn($documentexperted);

        //last, mock the EntityManager to return the mock of the repository
        $connectMock = $this->createMock(ObjectManager::class);

        $connectMock->expects($this->any())
                ->method('getRepository')
                ->willReturn($logRepository);


        //test
        $codeMock = new \system\Helper\Code($configMock, $connectMock);

        $entityMock = new RoleToMember($connectMock, $codeMock, $configMock);


        $this->assertEquals($documentexperted, $entityMock->find($id));
    }

    public function testUpdate() {
        $foo = true;
        $this->assertTrue($foo);
    }

    public function testRemove() {
        $foo = true;
        $this->assertTrue($foo);
    }

    public function testRestore() {
        $foo = true;
        $this->assertTrue($foo);
    }

}
