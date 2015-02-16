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

use DTL\Component\Content\Structure\Property;

class Structure
{
    /**
     * The resource from which this structure was loaded
     * (useful for debugging)
     *
     * @var string
     */
    public $resource;

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
    public $type;

    /**
     * Tags for this structure (are these used?)
     *
     * @var string[]
     */
    public $tags;

    /**
     * @var PropertyMetadata[]
     */
    public $children;

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

    public function __set($field, $value)
    {
        throw new \InvalidArgumentException(sprintf(
            'Property "%s" does not exist on "%s"',
            $field, get_class($this)
        ));
    }

    /**
     * Return the named property
     *
     * @return string $name
     */
    public function getChild($name)
    {
        if (!isset($this->children[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown child "%s" in structure loaded from: "%s"',
                $name, $this->resource
            ));
        }

        return $this->children[$name];
    }

    /**
     * Return all the localized children
     *
     * @return Property[]
     */
    public function getLocalizedProperties()
    {
        return array_filter($this->children, function (Property $property) {
            return $property->localized === true;
        });
    }

    /**
     * Return all the non-localized children
     *
     * @return Property[]
     */
    public function getNonLocalizedProperties()
    {
        return array_filter($this->children, function (Property $property) {
            return $property->localized === false;
        });
    }
}
