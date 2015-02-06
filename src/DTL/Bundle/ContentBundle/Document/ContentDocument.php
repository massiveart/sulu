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
use DTL\Component\Content\Model\ContentInterface;

/**
 * Base Structure class.
 *
 * Page and Snippet Documents will extend this class.
 *
 * @PHPCR\MappedSuperclass(
 *     translator="attribute"
 * )
 */
class ContentDocument implements ContentInterface
{
    /**
     * @PHPCR\Locale()
     */
    private $locale;

    /**
     * @PHPCR\NodeName()
     */
    private $name;

    /**
     * @PHPCR\ParentDocument()
     */
    private $parent;

    /**
     * @PHPCR\Children()
     */
    private $children;

    /**
     * @PHPCR\String(translated=true, translated=true)
     */
    private $title;

    /**
     * @PHPCR\String(translated=true, translated=true)
     */
    private $contentType;

    /**
     * @PHPCR\Long(nullable=true)
     */
    private $creator;

    /**
     * @PHPCR\Long(nullable=true)
     */
    private $changer;

    /**
     * @PHPCR\Date(nullable=true)
     */
    private $created;

    /**
     * @PHPCR\Date(nullable=true)
     */
    private $updated;

    /**
     * @PHPCR\String()
     */
    private $resourceLocator;

    /**
     * Content data.
     * This is not mapped, it is serialized by event listener.
     *
     * @see DTL\Component\Content\EventSubscriber\PhpcrOdmStructureSubscriber
     */
    private $content = array();

    /**
     * @var string
     */
    private $path;

    /**
     * @PHPCR\Uuid()
     */
    private $uuid;

    public function getParent() 
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getChildren() 
    {
        return $this->children;
    }
    
    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getTitle() 
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContentType() 
    {
        return $this->contentType;
    }
    
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
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

    public function getUpdated() 
    {
        return $this->updated;
    }
    
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getName() 
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPath() 
    {
        return $this->path;
    }

    public function getResourceLocator() 
    {
        return $this->resourceLocator;
    }
    
    public function setResourceLocator($resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    public function getLocale() 
    {
        return $this->locale;
    }
    
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getUuid() 
    {
        return $this->uuid;
    }
    
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function getWebspace()
    {
        $match = preg_match('/^\/' . $this->getPath('base') . '\/(\w*)\/.*$/', $this->path, $matches);

        if ($match) {
            return $matches[1];
        }

        throw new \RuntimeException(sprintf(
            'Could not determine webspace for content document at "%s"',
            $this->path
        ));
    }
}
