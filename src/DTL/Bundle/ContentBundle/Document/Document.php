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
use DTL\Component\Content\Document\LocalizationState;
use DTL\Component\Content\PhpcrOdm\ContentContainer;

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
    protected $requestedLocale;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $structureType;

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
     * @var ContentContainer
     */
    protected $content;

    /**
     * @var string Hash of the content
     */
    protected $contentHash = '_';

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
    protected $workflowState;

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

    public function setRequestedLocale($locale)
    {
        $this->requestedLocale = $locale;
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
        $this->initContentContainer();

        // clone to avoid state change
        return clone $this->content;
    }

    /**
     * {@inheritDoc}
     */
    public function setContent($content)
    {
        $this->initContentContainer();

        if ($content instanceof ContentContainer) {
            $content = $content->getArrayCopy();
        }

        $this->content->exchangeArray($content);

        // TODO: Hack to force the UOW to recalculate the changeset
        //       We could remove this with: https://github.com/doctrine/phpcr-odm/issues/417
        $this->contentHash = md5(json_encode($content));
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
            // todo: this should not be mutable, but how can we tell the subscriber not
            //       to re-set this?
        }

        $this->documentNodeHelper = $documentNodeHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkflowState() 
    {
        return $this->workflowState;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocalizationState()
    {
        $locales = $this->getLocales();

        // if no requeted locale
        if (null === $this->requestedLocale) {
            return LocalizationState::AUTO;
        }

        if (in_array($this->requestedLocale, $locales)) {
            return LocalizationState::LOCALIZED;
        }

        return LocalizationState::GHOST;
    }

    /**
     * {@inheritDoc}
     */
    public function isLocalizationState($state)
    {
        if (!in_array($state, static::getValidLocalizationStates())) {
            throw new \InvalidArgumentException(sprintf(
                'Localization state "%s" not valid for document type "%s", valid states are: "%s"',
                $state, $this->getDocumentType(), implode('", "', static::getValidLocalizationStates())
            ));
        }

        $currentState = $this->getLocalizationState();

        return $currentState == $state;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocales()
    {
        $this->assertPersisted(__METHOD__);

        return $this->getDocumentNodeHelper()->getLocales($this->node);
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
            , $callingMethod));
        }
    }

    /**
     * {@inheritDoc}
     */
    static public function getValidLocalizationStates()
    {
        return array(
            LocalizationState::LOCALIZED,
            LocalizationState::GHOST
        );
    }

    protected function getDocumentNodeHelper()
    {
        if (null === $this->documentNodeHelper) {
            throw new \RuntimeException(sprintf(
                'Document node helper has not been set on document: "%s" (%s)',
                $this->getPath(), spl_object_hash($this)
            ));
        }

        return $this->documentNodeHelper;
    }

    /**
     * Initialize the content container if it is null - the constructor cannot
     * be used because Doctrine uses the Instantiator to instantiate the class.
     * The instantiator bypasses the constructor.
     *
     */
    private function initContentContainer()
    {
        if (null === $this->content) {
            $this->content = new ContentContainer();
        }
    }
}
