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
use Sulu\Component\Content\StructureInterface;
use DTL\Component\Content\Document\PageInterface;

/**
 * Page document class
 */
abstract class BasePageDocument extends Document implements PageInterface
{
    /**
     * @var integer
     */
    protected $publishedState;

    /**
     * @var \DateTime
     */
    protected $published;

    /**
     * @var integer
     */
    protected $workflowStage;

    /**
     * @var array
     */
    protected $navigationContexts;

    /**
     * @var string
     */
    protected $redirectType;

    /**
     * @var boolean
     */
    protected $shadowLocaleEnabled = false;

    /**
     * @var string
     */
    protected $shadowLocale;

    public function __construct()
    {
        $this->workflowStage = StructureInterface::STATE_TEST;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublishedState() 
    {
        return $this->publishedState;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setPublishedState($publishedState)
    {
        $this->publishedState = $publishedState;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublished() 
    {
        return $this->published;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkflowStage() 
    {
        return $this->workflowStage;
    }

    /**
     * {@inheritDoc}
     */
    public function setWorkflowStage($workflowStage)
    {
        $this->workflowStage = $workflowStage;
    }

    /**
     * {@inheritDoc}
     */
    public function getNavigationContexts() 
    {
        return $this->navigationContexts;
    }

    /**
     * {@inheritDoc}
     */
    public function setNavigationContexts(array $navigationContexts)
    {
        $this->navigationContexts = $navigationContexts;
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectType() 
    {
        return $this->redirectType;
    }

    /**
     * {@inheritDoc}
     */
    public function setRedirectType($redirectType)
    {
        $this->redirectType = $redirectType;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDocumentType()
    {
        return 'page';
    }

    /**
     * {@inheritDoc}
     */
    public function getShadowLocale() 
    {
        return $this->shadowLocale;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setShadowLocale($shadowLocale)
    {
        $this->shadowLocale = $shadowLocale;
    }

    /**
     * {@inheritDoc}
     */
    public function setShadowLocaleEnabled($shadowLocaleEnabled)
    {
        $this->shadowLocaleEnabled = $shadowLocaleEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function isShadowLocaleEnabled()
    {
        return $this->shadowLocaleEnabled;
    }

    public function getEnabledShadowLocales()
    {
        if (null === $this->node) {
            throw new \RuntimeException(
                'Cannot retrieve enabled shadow locales on a non-persisted page. The PHPCR node ' .
                'must be available'
            );
        }
    }
}
