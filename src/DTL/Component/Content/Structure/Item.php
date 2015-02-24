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

class Item
{
    /**
     * Name of this item
     *
     * @var string
     */
    public $name;

    /**
     * The title of this property|structure e.g. [["de": "Artikles", "en": "Articles"]]
     *
     * @var array
     */
    public $title = array();

    /**
     * Description of this property|structure e.g. [["de": "Liste von Artikeln", "en": "List of articles"]]
     *
     * @var array
     */
    public $description = array();

    /**
     * Tags, e.g. [['name' => 'sulu_search.field', 'type' => 'string']]
     *
     * @var array
     */
    public $tags = array();

    /**
     * Parameters applying to the property
     *
     * e.g.
     *
     * {
     *     colspan: 6
     *     css_class: green-giant
     *     placeholder: Enter some text
     * }
     *
     * @var array
     */
    public $parameters = array();

    /**
     * Magic _set to catch undefined property accesses
     */
    public function __set($name, $value)
    {
        throw new \InvalidArgumentException(sprintf(
            'Property "%s" does not exist on "%s"',
            $name, get_class($this)
        ));
    }

    /**
     * Return the localized name of this Item or
     * default to the name.
     *
     * @param string $locale Localization
     *
     * @return string
     */
    public function getLocalizedTitle($locale)
    {
        if (isset($this->title[$locale])) {
            return $this->title[$locale];
        }

        return ucfirst($this->name);
    }
}
