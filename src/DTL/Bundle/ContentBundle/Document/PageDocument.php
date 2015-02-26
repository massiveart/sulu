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
class PageDocument extends Document implements PageInterface
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
}
