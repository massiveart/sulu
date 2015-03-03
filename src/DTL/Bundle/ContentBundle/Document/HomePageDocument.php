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
use DTL\Component\Content\Document\DocumentInterface;
use DTL\Component\Content\Document\PageInterface;
use PHPCR\NodeInterface;

/**
 * Base document class.
 */
class HomePageDocument extends BasePageDocument
{
    public function setName($name)
    {
        $this->name = $name;
    }
}

