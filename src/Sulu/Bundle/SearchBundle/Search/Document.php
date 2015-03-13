<?php

namespace Sulu\Bundle\SearchBundle\Search;

use Sulu\Component\Security\Authentication\UserInterface;
use Massive\Bundle\SearchBundle\Search\Document as BaseDocument;

/**
 * Custom search document class for Sulu which includes blame
 * and timestamp fields
 */
class Document extends BaseDocument
{
    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var string
     */
    protected $creatorName;

    /**
     * @var integer
     */
    protected $creatorId;

    /**
     * @var \DateTime
     */
    protected $changed;

    /**
     * @var string
     */
    protected $changerName;

    /**
     * @var integer
     */
    protected $changerId;

    /**
     * @return string
     */
    public function getCreated() 
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getChanged() 
    {
        return $this->changed;
    }
    
    /**
     * @param string $changed
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;
    }

    /**
     * @return string
     */
    public function getChangerName() 
    {
        return $this->changerName;
    }

    /**
     * @param string $changerName
     */
    public function setChangerName($changerName)
    {
        $this->changerName = $changerName;
    }

    /**
     * @return string
     */
    public function getCreatorName() 
    {
        return $this->creatorName;
    }

    /**
     * @param string $creatorName
     */
    public function setCreatorName($creatorName)
    {
        $this->creatorName = $creatorName;
    }

    /**
     * @return integer
     */
    public function getChangerId() 
    {
        return $this->changerId;
    }
    
    /**
     * @param integer $changerId
     */
    public function setChangerId($changerId)
    {
        $this->changerId = $changerId;
    }

    /**
     * @return integer
     */
    public function getCreatorId() 
    {
        return $this->creatorId;
    }
    
    /**
     * @param integer $creatorId
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;
    }
}