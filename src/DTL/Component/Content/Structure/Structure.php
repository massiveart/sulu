<?php

/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Structure;

class Structure
{
    /**
     * Translated label of this structure
     *
     * array('de'=> 'Wilkommen', 'fr' => 'Bienvenue')
     *
     * @var array
     */
    public $label = array();

    /**
     * Type of the structure e.g. overview
     *
     * @var string
     */
    public $structureType;


    /**
     * Frontend template to use for this structure
     *
     * @var string
     */
    public $view;

    /**
     * Controller to use to render this structure
     *
     * @var string
     */
    public $controller;

    /**
     * Cache lifetime (only applies to "pages")
     *
     * @var integer
     */
    public $cacheLifetime;

    /**
     * Tags for this structure (are these used?)
     *
     * @var string[]
     */
    public $tags;

    /**
     * @var PropertyMetadata[]
     */
    public $properties;

    public function __set($field, $value)
    {
        throw new \InvalidArgumentException(sprintf(
            'Property "%s" does not exist on "%s"',
            $field, get_class($this)
        ));
    }
}
