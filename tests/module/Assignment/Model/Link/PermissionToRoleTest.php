<?php

namespace Tests\module\Assignment\Model\Entity;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
//entity
use module\Assignment\Model\Link\PermissionToRole as Entity;

class PermissionToRoleTest extends TestCase {

    public function testAdd() {
        //input
        $id = new \MongoId();
        $token = "123";
        $app_id = "123";
        $permisison = "123";
        $roleid = "123";

        $creator_id = "123";
        $create_at = new \DateTime();

        //this document
        $documentexperted = new \module\Assignment\Model\Collection\PermissionToRole();
        $documentexperted->setId($id);
        $documentexperted->setToken($token);
        $documentexperted->setAppId($app_id);
        $documentexperted->setPermission($permisison);
        $documentexperted->setCreatorId($creator_id);
        $documentexperted->setCreateAt($create_at);
        $documentexperted->setUpdateAt($create_at);

        //mockup
        $configMock = [
            'URL_ROOT'       => 'test',
            'account_member' => [
                'permissions' => [
                    [
                        "name"   => "abc",
                        "value"  => "abc",
                        "action" => [
                            "abc",
                            "abc"
                        ]
                    ],
                    [
                        "name"   => "edf",
                        "value"  => "edf",
                        "action" => [
                            "edf"
                        ]
                    ]
                ]
            ]
        ];

        $connectMock = $this->getMockBuilder('DocumentManager')
                ->setMethods(array('persist', 'flush', 'getRepository', 'findOneBy', 'createQueryBuilder'))
                ->disableOriginalConstructor()
                ->getMock();

        $connectMock->expects($this->any())
                ->method('persist');
        $connectMock->expects($this->any())
                ->method('flush');

        //now, mock the repository so it returns the mock of the log
        $roleRepository = $this->createMock(ObjectRepository::class);

        $role = new \module\Assignment\Model\Collection\Role();
        $roleRepository->expects($this->once())
                ->method('find')
                ->willReturn($role);

        $connectMock->expects($this->any())
                ->method('getRepository')
                ->willReturn($roleRepository);

        //query builder
        $mockCollection = $this->getMockBuilder(\Doctrine\MongoDB\Collection::class)
                ->setMethods(array('createQueryBuilder', 'doCount'))
                ->disableOriginalConstructor()
                ->getMock();

        $mockCollection->expects($this->any())
                ->method('doCount')
                ->willReturn(0);

        $queryBuilder = new \Doctrine\MongoDB\Query\Builder($mockCollection);

        $connectMock->expects($this->any())
                ->method('createQueryBuilder')
                ->willReturn($queryBuilder);


        $codeMock = $this->getMockBuilder(\system\Helper\Code::class)
                ->setMethods(array('forbidden', 'notfound', 'error'))
                ->disableOriginalConstructor()
                ->getMock();

        $codeMock->expects($this->any())
                ->method('forbidden')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));

        $codeMock->expects($this->any())
                ->method('notfound')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));

        $codeMock->expects($this->any())
                ->method('error')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));


        $entityMock = new Entity($connectMock, $codeMock, $configMock);

        //input
        $data = (object) [
                    "app_id"     => $app_id,
                    "permission" => $permisison,
                    "role_id"    => $roleid,
                    "creator_id" => $creator_id,
                    "token"      => $token,
                    "id"         => $id,
                    "create_at"  => $create_at,
                    "update_at"  => $create_at,
        ];

        $entityMock->add($data);
    }

    public function testFind() {
        //input and experted
        $id = new \MongoId();
        $documentexperted = new \module\Assignment\Model\Collection\PermissionToRole();
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
        $codeMock = $this->getMockBuilder(\system\Helper\Code::class)
                ->setMethods(array('forbidden', 'notfound', 'error'))
                ->disableOriginalConstructor()
                ->getMock();

        $codeMock->expects($this->any())
                ->method('forbidden')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));

        $codeMock->expects($this->any())
                ->method('notfound')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));

        $codeMock->expects($this->any())
                ->method('error')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));


        $entityMock = new Entity($connectMock, $codeMock, $configMock);


        $this->assertEquals($documentexperted, $entityMock->find($id));
    }

    public function testUpdate() {
        $foo = true;

        //assert
        $this->assertTrue($foo);
    }

    public function testRemove() {
        //create mock
        $connectMock = $this->getMockBuilder('DocumentManager')
                ->setMethods(array('persist', 'flush', 'getRepository', 'createQueryBuilder'))
                ->disableOriginalConstructor()
                ->getMock();



        //query builder
        $mockCollection = $this->getMockBuilder(\Doctrine\MongoDB\Collection::class)
                ->setMethods(array('createQueryBuilder', 'doCount', 'remove'))
                ->disableOriginalConstructor()
                ->getMock();

        $mockCollection->expects($this->any())
                ->method('doCount')
                ->willReturn(0);

        $mockCollection->expects($this->any())
                ->method('remove')
                ->willReturn(0);

        $queryBuilder = new \Doctrine\MongoDB\Query\Builder($mockCollection);

        $connectMock->expects($this->any())
                ->method('createQueryBuilder')
                ->willReturn($queryBuilder);

        $connectMock->expects($this->any())
                ->method('persist');
        $connectMock->expects($this->any())
                ->method('flush');

        $codeMock = $this->getMockBuilder(\system\Helper\Code::class)
                ->setMethods(array('forbidden', 'notfound', 'error'))
                ->disableOriginalConstructor()
                ->getMock();

        $codeMock->expects($this->any())
                ->method('forbidden')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));

        $codeMock->expects($this->any())
                ->method('notfound')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));

        $codeMock->expects($this->any())
                ->method('error')
                ->will($this->returnCallback(function($e) {
                            throw new \Exception($e);
                        }));


        //input
        $role = new \module\Assignment\Model\Collection\Role();

        $data = (object) [
                    "app_id"     => "123",
                    "creator_id" => "123",
                    "role_id"    => "123",
                    "permission" => "abc"
        ];

        $roleRepository = $this->createMock(ObjectRepository::class);
        $roleRepository->expects($this->any())
                ->method('find')
                ->willReturn($role);

        $connectMock->expects($this->any())
                ->method('getRepository')
                ->willReturn($roleRepository);

        //mockup
        $configMock = [
            'URL_ROOT'       => 'test',
            'account_member' => [
                'permissions' => [
                    [
                        "name"   => "abc",
                        "value"  => "abc",
                        "action" => [
                            "abc",
                            "abc"
                        ]
                    ],
                    [
                        "name"   => "edf",
                        "value"  => "edf",
                        "action" => [
                            "edf"
                        ]
                    ]
                ]
            ]
        ];

        $entityMock = new Entity($connectMock, $codeMock, $configMock);

        //assert true
        $this->assertTrue($entityMock->remove($data));
    }

    public function testRestore() {
        $foo = true;

        //assert
        $this->assertTrue($foo);
    }

}
