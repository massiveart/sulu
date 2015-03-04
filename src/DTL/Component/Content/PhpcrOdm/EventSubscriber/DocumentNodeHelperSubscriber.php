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

use Doctrine\Common\EventSubscriber;
use DTL\Component\Content\PhpcrOdm\NamespaceRoleHelper;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use DTL\Component\Content\Document\DocumentInterface;
use Doctrine\ODM\PHPCR\Event;
use DTL\Component\Content\PhpcrOdm\DocumentNodeHelper;

class DocumentNodeHelperSubscriber implements EventSubscriber
{
    private $helper;

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Event::postLoad
        );
    }

    /**
     * @param NamespaceRoleHelper $helper
     */
    public function __construct(DocumentNodeHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $document = $event->getObject();

        if (!$document instanceof DocumentInterface) {
            return;
        }

        $document->setDocumentNodeHelper($this->helper);
    }
}
