<?php

namespace DTL\Component\Content\EventSubscriber;

use DTL\Bundle\ContentBundle\Document\DocumentName;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\Event;
use PHPCR\Util\UUIDHelper;
use Symfony\Cmf\Bundle\CoreBundle\Slugifier\SlugifierInterface;
use DTL\Component\Content\Document\DocumentInterface;

/**
 * Manage the name of the document (node) before persisting.
 *
 * The document should have a unique name based upon the primary
 * locale.
 *
 * TODO: This class should use the primary locale, current it changes
 *       the node name each time a specific locale is persisted.
 */
class NameSubscriber implements EventSubscriber
{
    private $documentManager;
    private $slugifier;

    /**
     * @param DocumentManager $documentManager
     * @param SlugifierInterface $slugifier
     */
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

    /**
     * Handle prePersist
     *
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $this->handleDocumentName($event);
    }

    /**
     * Handle prePersist
     *
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->handleDocumentName($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    private function handleDocumentName(LifecycleEventArgs $event)
    {
        $document = $event->getObject();

        if (!$document instanceof DocumentInterface) {
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
            throw new \InvalidArgumentException(sprintf(
                'Document with title "%s" has no parent',
                $title
            ));
        }

        if (!is_object($parent)) {
            throw new \InvalidArgumentException(sprintf(
                'Non-object detected as parent for document with title "%s", got "%s"',
                $title,
                gettype($parent)
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

    /**
     * Return the slugified name of the document after checking for and
     * resolving any conflicts.
     *
     * @param Document $document
     * @param Document $parent
     * @param string $title
     */
    private function getName($document, $parent, $title)
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

    /**
     * @param string $parentPath
     * @param string $slug
     * @param integer $index
     */
    private function getPath($parentPath, $slug, $index) 
    {
        $path = join('/', array($parentPath, $slug));

        if ($index > 0) {
            $path .= '-' . $index;
        }

        return $path;
    }
}
