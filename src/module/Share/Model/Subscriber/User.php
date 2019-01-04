<?php

namespace module\Share\Model\Subscriber;

use Doctrine\ODM\MongoDB\SoftDelete\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\SoftDelete\Events;

class User implements \Doctrine\Common\EventSubscriber {

    public function preSoftDelete(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();
        if ($document instanceof \module\Share\Model\Collection\User) {
            $sdm->deleteBy('module\Share\Model\Collection\Member', array('user.id' => $document->getId()));
        }
    }

    public function preRestore(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();
        if ($document instanceof \module\Share\Model\Collection\User) {
            $sdm->restoreBy('module\Share\Model\Collection\Member', array('user.id' => $document->getId()));
        }
    }

    public function getSubscribedEvents() {
        return array(
            Events::preSoftDelete,
            Events::preRestore
        );
    }

}
