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

/**
 * Base document class.
 */
class Document implements DocumentInterface
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

    public function getName()
    {
        return $this->name;
    }

    public function getTitle() 
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->name = $title; // set with ev listener
        $this->title = $title;
    }

    public function getUuid() 
    {
        return $this->uuid;
    }

    public function getPath() 
    {
        return $this->path;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent() 
    {
        return $this->parent;
    }

    public function getChildren() 
    {
        return $this->children;
    }

    public function getLocale() 
    {
        return $this->locale;
    }
    
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getStructureType() 
    {
        return $this->structureType;
    }
    
    public function setStructureType($structureType)
    {
        $this->structureType = $structureType;
    }

    public function getResourceLocator() 
    {
        return $this->resourceLocator;
    }
    
    public function setResourceLocator($resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    public function getCreator() 
    {
        return $this->creator;
    }
    
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    public function getChanger() 
    {
        return $this->changer;
    }
    
    public function setChanger($changer)
    {
        $this->changer = $changer;
    }

    public function getCreated() 
    {
        return $this->created;
    }
    
    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getChanged() 
    {
        return $this->changed;
    }
    
    public function setChanged($changed)
    {
        $this->changed = $changed;
    }

    public function getContent() 
    {
        return $this->content;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getWebspaceKey()
    {
        $match = preg_match('/^\/.*?\/(\w*)\/.*$/', $this->path, $matches);

        if ($match) {
            return $matches[1];
        }

        return null;
    }

    public function __toString()
    {
        return $this->path;
    }
}
