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
