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
use DTL\Component\Content\PhpcrOdm\EventSubscriber\Marker\AutoNameMarker;

/**
 * Page document represents a standard page.
 */
class PageDocument extends BasePageDocument implements AutoNameMarker
{
}
