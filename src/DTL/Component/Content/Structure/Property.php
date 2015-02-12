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
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $label = array();

    /**
     * @var array
     */
    public $formOptions = array();

    /**
     * @var string
     */
    public $type;

    /**
     * @var integer
     */
    public $minOccurs = 1;

    /**
     * @var mixed
     */
    public $maxOccurs = 1;

    /**
     * @var boolean
     */
    public $translated = false;

    /**
     * @var array
     */
    public $tags = array();

    /**
     * @var array
     */
    public $params = array();

    /**
     * @var integer
     */
    public $colspan;

    /**
     * @var string
     */
    public $cssClass;

    /**
     * @var array
     */
    public $children = array();

    /**
     * @var boolean
     */
    public $required;

    public function __set($field, $value)
    {
        throw new \InvalidArgumentException(sprintf(
            'Property "%s" does not exist on "%s"',
            $field, get_class($this)
        ));
    }
}
