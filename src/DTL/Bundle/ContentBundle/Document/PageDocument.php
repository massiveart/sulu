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

/**
 * Page document class
 */
class PageDocument extends Document
{
    /**
     * @var integer
     */
    protected $state = 0;

    public function getState() 
    {
        return $this->state;
    }
    
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentType()
    {
        return 'page';
    }

    public function getLocales()
    {
        $locales = array();

        $node = $this->getPhpcrNode();
        if (!$node) {
            return array($this->getLocale());
        }

        foreach ($node->getProperties() as $property) {
            /** @var PropertyInterface $property */
            preg_match('/^' . 'i18n' . ':([a-zA-Z_]*?)-title/', $property->getName(), $matches);

            if ($matches) {
                $locales[$matches[1]] = $matches[1];
            }
        }

        return array_values($locales);
    }

}
