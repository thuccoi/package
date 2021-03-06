<?php

namespace Tests\module\Assignment\Model\Entity;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
//entity
use module\Assignment\Model\Link\RoleToMember as Entity;

class RoleToMemberTest extends TestCase {

    public function testAdd() {
        //input
        $id = new \MongoId();
        $token = "123";
        $app_id = "app_123";
        $member_id = "member123";
        $roleid = "role123";

        $creator_id = "123";
        $create_at = new \DateTime();

        $role = new \module\Assignment\Model\Collection\Role();

        $app = new \module\Share\Model\Collection\App();
        $user = new \module\Share\Model\Collection\User();
        $member = new \module\Share\Model\Collection\Member($app, $user);

        //this document
        $documentexperted = new \module\Assignment\Model\Collection\RoleToMember();
        $documentexperted->setId($id);
        $documentexperted->setToken($token);
        $documentexperted->setAppId($app_id);
        $documentexperted->setCreatorId($creator_id);
        $documentexperted->setCreateAt($create_at);
        $documentexperted->setUpdateAt($create_at);

        $documentexperted->setRole($role);
        $documentexperted->setMember($member);

        //mockup
        $configMock = [
            'URL_ROOT'       => 'test',
            'DIR_ROOT'       => 'test',
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



        $roleRepository->expects($this->any())
                ->method('find')
                ->will($this->returnCallback(function($e) use($member_id, $member, $roleid, $role, $app_id, $app) {

                            if ($e == $member_id) {
                                return $member;
                            }

                            if ($e == $roleid) {
                                return $role;
                            }

                            if ($e == $app_id) {
                                return $app;
                            }

                            return null;
                        }));

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

        $sessionMock = $this->getMockBuilder(\system\Session::class)
                ->setMethods(array('set', 'get'))
                ->disableOriginalConstructor()
                ->getMock();
        $entityMock = new Entity($connectMock, $codeMock, $configMock, $sessionMock);

        //input
        $data = (object) [
                    "app_id"     => $app_id,
                    "member_id"  => $member_id,
                    "role_id"    => $roleid,
                    "creator_id" => $creator_id,
                    "token"      => $token,
                    "id"         => $id,
                    "create_at"  => $create_at,
                    "update_at"  => $create_at,
        ];

        $this->assertEquals($documentexperted, $entityMock->add($data));
    }

    public function testFind() {
        //input and experted
        $id = new \MongoId();
        $documentexperted = new \module\Assignment\Model\Collection\RoleToMember();
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

        $session = new \system\Session($configMock);
        $entityMock = new Entity($connectMock, $codeMock, $configMock, $session);


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

        $role_id = "role_123";
        $member_id = "member_123";

        $app = new \module\Share\Model\Collection\App();
        $user = new \module\Share\Model\Collection\User();
        $member = new \module\Share\Model\Collection\Member($app, $user);

        $data = (object) [
                    "app_id"     => "123",
                    "creator_id" => "123",
                    "role_id"    => $role_id,
                    "member_id"  => $member_id
        ];

        $roleRepository = $this->createMock(ObjectRepository::class);

        $roleRepository->expects($this->any())
                ->method('find')
                ->will($this->returnCallback(function($e) use($member_id, $member, $role_id, $role) {
                            if ($e == $member_id) {
                                return $member;
                            }
                            if ($e == $role_id) {
                                return $role;
                            }

                            return null;
                        }));

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

        $session = new \system\Session($configMock);
        $entityMock = new Entity($connectMock, $codeMock, $configMock, $session);

        //assert true
        $this->assertTrue($entityMock->remove($data));
    }

    public function testRestore() {
        $foo = true;

        //assert
        $this->assertTrue($foo);
    }

}
