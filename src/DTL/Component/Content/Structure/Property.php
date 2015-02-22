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

class Property
{
    /**
     * Type of this property (e.g. "text_line", "smart_content")
     *
     * @var string
     */
    public $type;

    /**
     * If the property should be available in different localizations
     *
     * @var boolean
     */
    public $options = false;

    /**
     * Tags, e.g. [['name' => 'sulu_search.field', 'type' => 'string']]
     *
     * @var array
     */
    public $tags = array();

    /**
     * Children of this property, could array of either Property or Structure
     * objects.
     *
     * @var Property|Structure[]
     */
    public $children = array();

    /**
     * @var integer
     */
    public $minOccurs = 1;

    /**
     * @var mixed
     */
    public $maxOccurs = 1;

    public function __set($field, $value)
    {
        throw new \InvalidArgumentException(sprintf(
            'Property "%s" does not exist on "%s"',
            $field, get_class($this)
        ));
    }
}
