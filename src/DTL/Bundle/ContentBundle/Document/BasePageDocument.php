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
use DTL\Component\Content\Document\WorkflowState;
use DTL\Component\Content\Document\LocalizationState;

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
     * @var array
     */
    protected $navigationContexts;

    /**
     * @var string
     */
    protected $redirectType;

    /**
     * @var PageInterface
     */
    protected $redirectTarget;

    /**
     * @var string
     */
    protected $redirectExternal;

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
        $this->workflowState = WorkflowState::TEST;
    }

    /**
     * {@inheritDoc}
     */
    public function isPublished() 
    {
        return WorkflowState::isPublished($this->workflowState);
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
    public function setWorkflowState($workflowState)
    {
        $this->updatePublishedDate($workflowState);
        $this->workflowState = $workflowState;
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
    public function setRedirectType($redirectType = null)
    {
        $this->redirectType = $redirectType;
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectExternal() 
    {
        return $this->redirectExternal;
    }

    /**
     * {@inheritDoc}
     */
    public function setRedirectExternal($redirectExternal = null)
    {
        $this->redirectExternal = $redirectExternal;
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectTarget() 
    {
        return $this->redirectTarget;
    }

    /**
     * {@inheritDoc}
     */
    public function setRedirectTarget(PageInterface $redirectTarget = null)
    {
        $this->redirectTarget = $redirectTarget;
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
    public function getShadowLocaleEnabled() 
    {
        return $this->shadowLocaleEnabled;
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
    public function getShadowLocales()
    {
        $this->assertPersisted(__METHOD__);

        return $this->documentNodeHelper->getEnabledShadowLocales($this->node);
    }

    /**
     * {@inheritDoc}
     */
    public function getRealLocales()
    {
        $this->assertPersisted(__METHOD__);

        $locales = $this->documentNodeHelper->getLocales($this->node);
        $shadowLocales = $this->documentNodeHelper->getEnabledShadowLocales($this->node);
        $realLocales = array_diff($locales, $shadowLocales);

        return $realLocales;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocalizationState()
    {
        if (true === $this->shadowLocaleEnabled) {
            return LocalizationState::SHADOW;
        }

        return parent::getLocalizationState();
    }

    /**
     * {@inheritDoc}
     */
    static public function getValidLocalizationStates()
    {
        return array(
            LocalizationState::LOCALIZED,
            LocalizationState::GHOST,
            LocalizationState::SHADOW
        );
    }

    /**
     * If the current workflow state is not published, and the
     * new one is published, set the published date.
     *
     * @param string $workflowState
     */
    private function updatePublishedDate($workflowState)
    {
        WorkflowState::validateState($workflowState);
        $newPublished = WorkflowState::isPublished($workflowState);

        // Set the published date
        if (false === $this->isPublished() && true === $newPublished) {
            $this->published = new \DateTime();
        }
    }
}
