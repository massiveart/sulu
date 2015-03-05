<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;
use DTL\Component\Content\Document\DocumentInterface;
use DTL\Component\Content\Document\PageInterface;
use PHPCR\NodeInterface;
use DTL\Component\Content\PhpcrOdm\NamespaceRoleRegistry;
use DTL\Component\Content\PhpcrOdm\DocumentNodeHelper;

/**
 * Base document class.
 */
abstract class Document implements DocumentInterface
{
    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $parent;

    /**
     * @var object
     */
    protected $parentDocument;

    /**
     * @var ChildrenCollection
     */
    protected $children;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $structureType;

    /**
     * @var string
     */
    protected $resourceLocator;

    /**
     * @var integer
     */
    protected $creator;

    /**
     * @var integer
     */
    protected $changer;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $changed;

    /**
     * @var array Not mapped, populated in an event listener
     */
    protected $content = array();

    /**
     * @var DocumentNodeHelper
     */
    protected $documentNodeHelper;

    /**
     * @var NodeInterface
     */
    protected $node;

    /**
     * @var integer
     */
    protected $depth;

    /**
     * @var string
     */
    protected $lifecycleStage;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle() 
    {
        return $this->title;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritDoc}
     */
    public function getUuid() 
    {
        return $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath() 
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent() 
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren() 
    {
        return $this->children;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocale() 
    {
        return $this->locale;
    }

    /**
     * {@inheritDoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritDoc}
     */
    public function getStructureType() 
    {
        return $this->structureType;
    }

    /**
     * {@inheritDoc}
     */
    public function setStructureType($structureType)
    {
        $this->structureType = $structureType;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceLocator() 
    {
        return $this->resourceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function setResourceLocator($resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreator() 
    {
        return $this->creator;
    }

    /**
     * {@inheritDoc}
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * {@inheritDoc}
     */
    public function getChanger() 
    {
        return $this->changer;
    }

    /**
     * {@inheritDoc}
     */
    public function setChanger($changer)
    {
        $this->changer = $changer;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreated() 
    {
        return $this->created;
    }

    /**
     * {@inheritDoc}
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * {@inheritDoc}
     */
    public function getChanged() 
    {
        return $this->changed;
    }

    /**
     * {@inheritDoc}
     */
    public function setChanged(\DateTime $changed)
    {
        $this->changed = $changed;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent() 
    {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getWebspaceKey()
    {
        $match = preg_match('/^\/.*?\/(\w*)\/.*$/', $this->path, $matches);

        if ($match) {
            return $matches[1];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpcrNode()
    {
        return $this->node;
    }

    /**
     * {@inheritDoc}
     */
    public function getDepth() 
    {
        return $this->depth;
    }
    

    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return count($this->children) ? true : false;
    }

    /**
     * {@inheritDoc}
     */
    public function setDocumentNodeHelper(DocumentNodeHelper $documentNodeHelper)
    {
        if (null !== $this->documentNodeHelper) {
            throw new \RuntimeException(
                'Document helper has already been set. Cannot replace it.'
            );
        }

        $this->documentNodeHelper = $documentNodeHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getLifecycleStage() 
    {
        return $this->lifecycleStage;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocalizationState()
    {
        $locales = $this->getLocales();
        var_dump($locales);
        var_dump($this->getLocale());

        if (in_array($this->getLocale(), $locales)) {
            return DocumentInterface::LOCALIZATION_STATE_LOCALIZED;
        }

        return DocumentInterface::LOCALIZATION_STATE_GHOST;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocales()
    {
        $this->assertPersisted(__METHOD__);

        return $this->documentNodeHelper->getLocales($this->node);
    }

    /**
     * Magic __toString method.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->path ? : get_class($this);
    }

    /**
     * Assert that the PHPCR node is present (i.e. that the document
     * has been already been persisted).
     *
     * @param string $callingMethod
     */
    protected function assertPersisted($callingMethod)
    {
        if (null === $this->node) {
            throw new \RuntimeException(sprintf(
                'Method "%s" requires access to the PHPCR node, which is only ' .
                'available when document has been persisted.'
            ), $callingMethod);
        }
    }
}
