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

class RootStructure extends Structure
{
    /**
     * The resource from which this structure was loaded
     * (useful for debugging)
     *
     * @var string
     */
    public $resource;

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
}

