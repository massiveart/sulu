<?php

namespace DTL\Component\Content\EventSubscriber;

use DTL\Bundle\ContentBundle\Document\DocumentParent;
use DTL\Bundle\ContentBundle\Document\Document;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\Event;
use PHPCR\Util\UUIDHelper;

class DocumentParentSubscriber implements EventSubscriber
{
    private $documentManager;

    public function __construct(
        DocumentManager $documentManager
    ) {
        $this->documentManager = $documentManager;
    }

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

    public function prePersist(LifecycleEventArgs $event)
    {
        $this->handleDocumentParent($event);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->handleDocumentParent($event);
    }

    private function handleDocumentParent(LifecycleEventArgs $event)
    {
        $document = $event->getObject();

        if (!$document instanceof Document) {
            return;
        }

        $parent = $document->getParent();

        if (null === $parent) {
            throw new \InvalidArgumentException(sprintf(
                'No parent set on document "%s"',
                get_class($document)
            ));
        }

        $parent = $this->getParent($document, $parent);

        $meta = $this->documentManager->getClassMetadata(get_class($document));
        $meta->setFieldValue(
            $document,
            'parentDocument',
            $parent
        );
    }

    private function getParent($document, $parent)
    {
        // if its already an object
        if (is_object($parent)) {
            return $parent;
        }

        // if its a uuid
        if (UUIDHelper::isUUID($parent)) {
            return $this->getParentByUuid($document, $parent);
        }

        if (is_string($parent)) {
            return $this->getParentByPath($document, $parent);
        }

        throw new \InvalidArgumentException(sprintf(
            'Could not determine parent from value "%s"',
            $parent
        ));
    }

    private function getParentByUuid(Document $document, $uuid)
    {
        $parent = $this->documentManager->find(null, $uuid);

        if (null === $parent) {
            throw new \RuntimeException(sprintf(
                'Could not find parent by UUID "%s"',
                $parent
            ));
        }

        return $parent;
    }

    private function getParentByPath(Document $document, $path)
    {
        if (substr($path, 0, 1) !== '/') {
            throw new \InvalidArgumentException(sprintf(
                'Parent path must be given as an absolute path, was given "%s"',
                $path
            ));
        }

        $parent = $this->documentManager->find(null, $path);

        if (null === $parent) {
            throw new \RuntimeException(sprintf(
                'Could not find parent by path "%s"',
                $path
            ));
        }

        return $parent;
    }
}
