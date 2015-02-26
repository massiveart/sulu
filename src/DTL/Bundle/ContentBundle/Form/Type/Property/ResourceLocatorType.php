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
use DTL\Component\Content\Form\ContentView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DTL\Component\Content\Property\PropertyTypeInterface;
use DTL\Component\Content\FrontView\FrontView;

class ResourceLocatorType extends AbstractType implements PropertyTypeInterface
{
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'compound' => false,
        ));
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
        return 'resource_locator_property';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'property';
    }
}
