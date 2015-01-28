<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\EventSubscriber;

use Doctrine\ODM\PHPCR\Event;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use DTL\Component\Content\Serializer\SerializerInterface;
use DTL\Bundle\ContentBundle\Document\ContentDocument;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\ManagerEventArgs;
use DTL\Component\Content\Model\ContentInterface;

class ContentSerializerSubscriber implements EventSubscriber
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var ContentInterface[]
     */
    private $serializationStack = array();

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Event::postLoad,
            Event::prePersist,
            Event::preUpdate,
            Event::endFlush,
        );
    }

    /**
     * @param DocumentManager $documentManager
     * @param SerializerInterface $serializer
     */
    public function __construct(DocumentManager $documentManager, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->documentManager = $documentManager;
    }

    /**
     * Deserialize the content data after loading the document
     *
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $document = $event->getObject();

        if (false === $this->isContent($document)) {
            return;
        }

        $node = $this->documentManager->getNodeForDocument($document);
        $data = $this->serializer->deserialize($node);
        $document->setContent($data);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->prePersist($event);
    }

    /**
     * Serialize the content data before persisting the document
     *
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $document = $event->getObject();

        if (false === $this->isContent($document)) {
            return;
        }

        $this->serializationStack[] = $document;
    }

    /**
     * Update the PHPCR nodes with the new data after the flush
     * has happened (and we can be sure that the document is
     * now mapped to a PHPCR node.
     *
     * @param ManagerEventArgs $event
     */
    public function endFlush(ManagerEventArgs $event)
    {
        if (empty($this->serializationStack)) {
            return;
        }

        $session = $event->getObjectManager()->getPhpcrSession();

        foreach ($this->serializationStack as $document) {
            $node = $this->documentManager->getNodeForDocument($document);
            $this->serializer->serialize($document->getContent(), $node);
        }

        $session->save();
    }

    /**
     * @param mixed $object
     */
    private function isContent($object)
    {
        return $object instanceof ContentInterface;
    }
}
