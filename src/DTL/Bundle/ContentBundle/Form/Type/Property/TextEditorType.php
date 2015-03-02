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
use DTL\Component\Content\Form\ContentView;
use DTL\Component\Content\Property\PropertyTypeInterface;
use DTL\Component\Content\FrontView\FrontView;

class TextEditorType extends AbstractType implements PropertyTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'compound' => false,
            'god_mode' => false,
            'tables_enabled' => true,
            'links_enabled' => true,
            'paste_from_word' => true,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['god_mode'] = $options['god_mode'];
        $view->vars['tables_enabled'] = $options['tables_enabled'];
        $view->vars['links_enabled'] = $options['links_enabled'];
        $view->vars['paste_from_word'] = $options['paste_from_word'];
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
        return 'text_editor';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'property';
    }
}

