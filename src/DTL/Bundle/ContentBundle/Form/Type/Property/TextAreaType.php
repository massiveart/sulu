<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Form\Type\Property;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\Property\PropertyTypeInterface;
use DTL\Component\Content\FrontView\FrontView;

class TextAreaType extends AbstractType implements PropertyTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function buildFrontView(FrontView $view, $data, array $options)
    {
        $view->setValue($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'text_area_property';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text_line';
    }
}
