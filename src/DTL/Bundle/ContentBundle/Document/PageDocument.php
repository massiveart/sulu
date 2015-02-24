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

/**
 * Page document class
 */
class PageDocument extends Document
{
    /**
     * @var integer
     */
    private $publishedState;

    /**
     * @var \DateTime
     */
    private $published;

    /**
     * @var integer
     */
    private $workflowStage;

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
    public function getDocumentType()
    {
        return 'page';
    }
}
