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

    /**
     * Return the named property
     *
     * @return string $name
     */
    public function getProperty($name)
    {
        if (!isset($this->properties[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown property "%s" in structure loaded from: "%s"',
                $name, $this->resource
            ));
        }

        return $this->properties[$name];
    }

    /**
     * Return all the localized properties
     *
     * @return Property[]
     */
    public function getLocalizedProperties()
    {
        return array_filter($this->properties, function (Property $property) {
            return $property->localized === true;
        });
    }

    /**
     * Return all the non-localized properties
     *
     * @return Property[]
     */
    public function getNonLocalizedProperties()
    {
        return array_filter($this->properties, function (Property $property) {
            return $property->localized === false;
        });
    }
}
