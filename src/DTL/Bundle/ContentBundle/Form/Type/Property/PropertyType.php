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
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Component\Content\Property\PropertyTypeInterface;
use DTL\Component\Content\FrontView\FrontView;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\OptionsResolver\Options;

/**
 * Base type for properties
 */
class PropertyType extends AbstractType implements PropertyTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setRequired(array(
            'label',
        ));

        $options->setDefaults(array(
            'tags' => array(),
            'priority' => 1,
            'translated' => true,
            'label' => array()
        ));

        $options->setAllowedTypes(array(
            'tags' => 'array'
        ));

        $options->setNormalizers(array(
            'tags' => function ($options, $value) {
                foreach ($value as &$tag) {
                    if (!is_array($tag)) {
                        $tag = array(
                            'name' => $tag,
                        );
                    }

                    if (!isset($tag['priority'])) {
                        $tag['priority'] = 1;
                    }
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
        // remove form_ prefix
        $view->vars['id'] = substr($view->vars['id'], 5);

        $view->vars['property'] = array(
            'name' => $form->getName(),
            'metadata' => array(
                'label' => $options['label'],
            ),
            'required' => $options['required'],
            'translated' => $options['translated'],
            'structureTypeName' => $this->getName(),
            'params' => array(),
            'tags' => $options['tags'],
        );

        $view->vars['label'] = $options['label'];
    }

    /**
     * {@inheritDoc}
     */
    public function buildFrontView(FrontView $view, $data, array $options)
    {
        $view->setValue($data);
    }

    public function getName()
    {
        return 'property';
    }
}
