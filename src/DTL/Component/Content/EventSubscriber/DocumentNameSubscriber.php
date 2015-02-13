<?php

namespace DTL\Component\Content\EventSubscriber;

use DTL\Bundle\ContentBundle\Document\DocumentName;
use DTL\Bundle\ContentBundle\Document\Document;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\Event;
use PHPCR\Util\UUIDHelper;
use Symfony\Cmf\Bundle\CoreBundle\Slugifier\SlugifierInterface;

class DocumentNameSubscriber implements EventSubscriber
{
    private $documentManager;
    private $slugifier;

    public function __construct(
        DocumentManager $documentManager,
        SlugifierInterface $slugifier
    ) {
        $this->documentManager = $documentManager;
        $this->slugifier = $slugifier;
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
        $this->handleDocumentName($event);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->handleDocumentName($event);
    }

    private function handleDocumentName(LifecycleEventArgs $event)
    {
        $document = $event->getObject();

        if (!$document instanceof Document) {
            return;
        }

        $title = $document->getTitle();

        if (!$title) {
            throw new \InvalidArgumentException(sprintf(
                'Document of class "%s" has no title',
                get_class($document)
            ));
        }

        $parent = $document->getParent();

        if (!$parent) {
            throw new \RuntimeException(sprintf(
                'Document with title "%s" has no parent',
                $title
            ));
        }

        if (!is_object($parent)) {
            throw new \InvalidArgumentException(sprintf(
                'Non-object detected as parent for document with title "%s"',
                $title
            ));
        }

        $name = $this->getName($document, $parent, $title);

        $meta = $this->documentManager->getClassMetadata(get_class($document));
        $meta->setFieldValue(
            $document,
            'name',
            $name
        );
    }

    public function getName($document, $parent, $title)
    {
        $slug = $this->slugifier->slugify($title);

        $parentPath = $this->documentManager->getUnitOfWork()->getDocumentId($parent, false);

        // no parent path, no children
        if (!$parentPath) {
            return $slug;
        }

        $i = 0;
        do {
            $path = $this->getPath($parentPath, $slug, $i++);
            $document = $this->documentManager->find(null, $path);
        } while ($document);

        return basename($path);
    }

    private function getPath($parentPath, $slug, $i) 
    {
        $path = join('/', array($parentPath, $slug));

        if ($i > 0) {
            $path .= '-' . $i;
        }

        return $path;
    }
}
