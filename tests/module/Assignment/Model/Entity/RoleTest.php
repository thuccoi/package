<?php

namespace Tests\module\Assignment\Model\Entity;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
//entity
use module\Assignment\Model\Entity\Role;

class RoleTest extends TestCase {

    public function testCreate() {
        //input
        $id = new \MongoId();
        $token = "123";
        $app_id = "123";
        $parentid = new \MongoId();
        $name = "123";
        $creator_id = "123";
        $metatype = "123";
        $create_at = new \DateTime();

        //expeted
        $parentexpected = new \module\Assignment\Model\Collection\Role();
        $parentexpected->setId($parentid);

        //this document
        $documentexperted = new \module\Assignment\Model\Collection\Role();
        $documentexperted->setId($id);
        $documentexperted->setToken($token);
        $documentexperted->setAppId($app_id);
        $documentexperted->setName($name);
        $documentexperted->setCreatorId($creator_id);
        $documentexperted->setMetatype($metatype);
        $documentexperted->setCreateAt($create_at);
        $documentexperted->setUpdateAt($create_at);
        $documentexperted->setParent($parentexpected);

        //mockup
        $configMock = [
            'URL_ROOT' => 'test'
        ];

        $connectMock = $this->getMockBuilder('DocumentManager')
                ->setMethods(array('persist', 'flush', 'getRepository', 'findOneBy'))
                ->disableOriginalConstructor()
                ->getMock();

        $connectMock->expects($this->any())
                ->method('persist');
        $connectMock->expects($this->any())
                ->method('flush');

        //now, mock the repository so it returns the mock of the log
        $roleRepository = $this->createMock(ObjectRepository::class);

        $roleRepository->expects($this->once())
                ->method('findOneBy')
                ->willReturn(null);

        $roleRepository->expects($this->once())
                ->method('find')
                ->willReturn($parentexpected);

        $connectMock->expects($this->any())
                ->method('getRepository')
                ->willReturn($roleRepository);


        $codeMock = new \system\Helper\Code($configMock, $connectMock);

        $entityMock = new Role($connectMock, $codeMock, $configMock);

        //input
        $data = (object) [
                    "app_id"     => $app_id,
                    "creator_id" => $creator_id,
                    "name"       => $name,
                    "metatype"   => $metatype,
                    "token"      => $token,
                    "id"         => $id,
                    "parent"     => (string) $parentid,
                    "create_at"  => $create_at,
                    "update_at"  => $create_at,
        ];

        //test
        $entityMock->create($data);
    }

    public function testFind() {
        //input and experted
        $id = new \MongoId();
        $documentexperted = new \module\Assignment\Model\Collection\Role();
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

        $entityMock = new Role($connectMock, $codeMock, $configMock);


        $this->assertEquals($documentexperted, $entityMock->find($id));
    }

    public function testEdit() {
        //input 
        $id = new \MongoId();
        $parent = new \MongoId();

        $name = "test";
        $description = "test";


        $app_id = "123";
        $creator_id = "123";
        $token = "123";
        $create_at = new \DateTime();

        $parentdocument = new \module\Assignment\Model\Collection\Role();
        $parentdocument->setId($parent);

        $olddocument = new \module\Assignment\Model\Collection\Role();
        $olddocument->setId($id);
        $olddocument->setToken($token);
        $olddocument->setCreateAt($create_at);
        $olddocument->setUpdateAt($create_at);

        $documentexperted = new \module\Assignment\Model\Collection\Role();
        $documentexperted->setId($id);
        $documentexperted->setName($name);
        $documentexperted->setDescription($description);
        $documentexperted->setToken($token);
        $documentexperted->setCreateAt($create_at);
        $documentexperted->setUpdateAt($create_at);
        $documentexperted->setParent($parentdocument);

        //mockup
        $configMock = [
            'URL_ROOT' => 'test'
        ];

        $connectMock = $this->getMockBuilder('DocumentManager')
                ->setMethods(array('persist', 'flush', 'getRepository'))
                ->disableOriginalConstructor()
                ->getMock();

        $connectMock->expects($this->any())
                ->method('persist');
        $connectMock->expects($this->any())
                ->method('flush');

        //now, mock the repository so it returns the mock of the log
        $roleRepository = $this->createMock(ObjectRepository::class);
        $roleRepository->expects($this->at(0))
                ->method('find')
                ->with($id)
                ->willReturn($olddocument);
        $roleRepository->expects($this->at(1))
                ->method('find')
                ->with($parent)
                ->willReturn($parentdocument);

        $connectMock->expects($this->any())
                ->method('getRepository')
                ->willReturn($roleRepository);

        $codeMock = new \system\Helper\Code($configMock, $connectMock);

        $entityMock = new Role($connectMock, $codeMock, $configMock);

        //input
        $data = (object) [
                    "name"        => $name,
                    "description" => $description,
                    "parent"      => (string) $parent,
                    "app_id"      => $app_id,
                    "creator_id"  => $creator_id
        ];

        //test
        $this->assertEquals($documentexperted, $entityMock->edit($id, $data));
    }

    public function testDelete() {
        $foo = true;
        $this->assertTrue($foo);
    }

    public function testRestore() {
        $foo = true;
        $this->assertTrue($foo);
    }

    public function testIsSpiderweb() {
        $foo = true;
        $this->assertTrue($foo);
    }

}
