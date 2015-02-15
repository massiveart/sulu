<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Type;

use Symfony\Component\Form\FormTypeInterface;
use DTL\Component\Content\Form\ContentView;
use Symfony\Component\Form\FormInterface;

/**
 * Form types implementing this interface become valid Sulu
 * content-types.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface ContentTypeInterface extends ContentTypeFormInterface, ContentTypeFrontInterface
{
}
