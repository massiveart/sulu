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
use DTL\Component\Content\Model\DocumentInterface;
use DTL\Component\Content\Model\PageInterface;
use PHPCR\NodeInterface;

/**
 * Base document class.
 */
abstract class Document implements PageInterface
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $parent;

    /**
     * @var object
     */
    private $parentDocument;

    /**
     * @var ChildrenCollection
     */
    private $children;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $structureType;

    /**
     * @var string
     */
    private $resourceLocator;

    /**
     * @var integer
     */
    private $creator;

    /**
     * @var integer
     */
    private $changer;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $changed;

    /**
     * @var array (not mapped, populated in an event listener)
     */
    private $content = array();

    /**
     * @var NodeInterface
     */
    private $node;

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
        $this->name = $title; // set with ev listener
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

    public function __toString()
    {
        return $this->path ? : get_class($this);
    }
}
