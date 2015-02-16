<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Component\Content\Type\ContentTypeInterface;
use DTL\Component\Content\FrontView\FrontView;

/**
 * Adds stubb implementation for content type interface contract
 */
abstract class AbstractContentType extends AbstractType implements ContentTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setRequired(array(
            'webspace_key',
            'locale',
            'label',
        ));

        $options->setDefaults(array(
            'legacy' => true,
            'tags' => array(),
            'min_occurs' => 1,
            'max_occurs' => 1,
            'priority' => 1,
            'translated' => true,
            'labels' => array(),
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
        $view->vars['webspace_key'] = $options['webspace_key'];
        $view->vars['locale'] = $options['locale'];

        if (true === $options['legacy']) {
            // remove form_ prefix
            $view->vars['id'] = substr($view->vars['id'], 5);

            $view->vars['property'] = array(
                'name' => $form->getName(),
                'metadata' => array(
                    'title' => $options['labels'],
                ),
                'mandatory' => $options['required'],
                'multilingual' => $options['translated'],
                'minOccurs' => $options['min_occurs'],
                'maxOccurs' => $options['max_occurs'],
                'contentTypeName' => $this->getName(),
                'params' => array(),
                'tags' => $options['tags'],
            );
        }
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
        return 'content';
    }
}
