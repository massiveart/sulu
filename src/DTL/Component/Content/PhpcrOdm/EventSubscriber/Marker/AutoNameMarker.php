<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\PhpcrOdm\EventSubscriber\Marker;

use DTL\Component\Content\Document\DocumentInterface;

/**
 * Classes implementing this marker will have their document names
 * automatically assigned based on their title
 */
interface AutoNameMarker extends DocumentInterface
{
}
