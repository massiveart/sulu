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

class TextLineType extends AbstractType implements PropertyTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'compound' => false,
            'placeholder' => array(),
        ));

        $options->setAllowedTypes(array(
            'placeholder' => array('array')
        ));

        $options->setNormalizers(array(
            'placeholder' => function ($options, $value) {
                if (!is_array($value)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Placeholder value must be an array of translations, e.g. array("de" => "Wilkkommen", "en" => "Welcome"), got "%s"',
                        print_r($value, true)
                    ));
                }

                return $value;
            }
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['placeholder'] = $options['placeholder'];
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
        return 'text_line';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'property';
    }
}
