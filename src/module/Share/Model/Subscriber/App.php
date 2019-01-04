<?php

namespace module\Share\Model\Subscriber;

use Doctrine\ODM\MongoDB\SoftDelete\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\SoftDelete\Events;

class App implements \Doctrine\Common\EventSubscriber {

    public function preSoftDelete(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();
        if ($document instanceof \module\Share\Model\Entity\App) {
            $sdm->deleteBy(\module\Share\Model\Link\Member::class, array('app.id' => $document->getId()));
        }
    }

    public function preRestore(LifecycleEventArgs $args) {
        $sdm = $args->getSoftDeleteManager();
        $document = $args->getDocument();
        if ($document instanceof \module\Share\Model\Entity\App) {
            $sdm->restoreBy(\module\Share\Model\Link\Member::class, array('app.id' => $document->getId()));
        }
    }

    public function getSubscribedEvents() {
        return array(
            Events::preSoftDelete,
            Events::preRestore
        );
    }

}
