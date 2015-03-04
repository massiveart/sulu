<?php 
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
namespace DTL\Component\Content\PhpcrOdm\EventSubscriber;

use DTL\Bundle\ContentBundle\Document\DocumentName;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\Event;
use PHPCR\Util\UUIDHelper;
use DTL\Component\Content\Document\DocumentInterface;

/**
 * Manage the timestamp (created, changed) fields on
 * documents before they are persisted.
 */
class TimestampSubscriber implements EventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Event::prePersist,
            Event::preUpdate
        );
    }

    /**
     * Handle prePersist
     *
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $this->handleTimestamp($event);
    }

    /**
     * Handle prePersist
     *
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->handleTimestamp($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    private function handleTimestamp(LifecycleEventArgs $event)
    {
        $document = $event->getObject();

        if (!$document instanceof DocumentInterface) {
            return;
        }

        if (!$document->getCreated()) {
            $document->setCreated(new \DateTime());
        }

        $document->setChanged(new \DateTime());
    }
}
