<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\Form\ResolvedFormType;

class ContentResolvedType extends ResolvedFormType implements ContentResolvedTypeInterface
{
    /**
     * @see DTL\Component\Content\Form\ContentTypeInterface#buildContentView
     *
     * @param ContentView $view
     * @param mixed $data
     */
    public function buildContentView(ContentView $view, $data)
    {
        return $this->getInnerType()->buildContentView($view, $data);
    }
}
