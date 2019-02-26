<?php

namespace Tests\module\Share\Model\Link;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
//entity
use module\Share\Model\Link\Member as Entity;

class RoleToMemberTest extends TestCase {

    public function testIsSpiderweb() {
        $app = new \module\Share\Model\Collection\App();
        $user = new \module\Share\Model\Collection\User();
        //input
        $rootid = new \MongoId();
        $root = new \module\Share\Model\Collection\Member($app, $user);
        $root->setId($rootid);

        $node1id = new \MongoId();
        $node1 = clone $root;
        $node1->setId($node1id);
        $node1->setManager($root);


        $node2id = new \MongoId();
        $node2 = clone $root;
        $node2->setId($node2id);
        $node2->setManager($node1);



        //create mock
        $connectMock = $this->getMockBuilder('DocumentManager')
                ->setMethods(array('persist', 'flush', 'getRepository'))
                ->disableOriginalConstructor()
                ->getMock();

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

        $configMock = [];
        $session = new \system\Session($configMock);
        $entityMock = new Entity($connectMock, $codeMock, $configMock, $session);

        //check false
        $this->assertFalse($entityMock->isSpiderweb($node1, $root));

        $this->assertFalse($entityMock->isSpiderweb($node2, $node1));

        $this->assertFalse($entityMock->isSpiderweb($node2, $root));

        //check true
        $this->assertTrue($entityMock->isSpiderweb($node1, $node1));
        $this->assertTrue($entityMock->isSpiderweb($node2, $node2));
        $this->assertTrue($entityMock->isSpiderweb($root, $root));
        $this->assertTrue($entityMock->isSpiderweb($root, $node1));
        $this->assertTrue($entityMock->isSpiderweb($root, $node2));
        $this->assertTrue($entityMock->isSpiderweb($node1, $node2));
    }

}
