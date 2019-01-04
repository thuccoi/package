<?php

namespace module\Share\Model\Subscriber;

use Doctrine\ODM\MongoDB\SoftDelete\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\SoftDelete\Events;

class Member implements \Doctrine\Common\EventSubscriber {

    public function preSoftDelete(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();
        if ($document instanceof \module\Share\Model\Link\Member) {
            
        }
    }

    public function preRestore(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();
        if ($document instanceof \module\Share\Model\Link\Member) {
            
        }
    }

    public function getSubscribedEvents() {
        return array(
            Events::preSoftDelete,
            Events::preRestore
        );
    }

}
