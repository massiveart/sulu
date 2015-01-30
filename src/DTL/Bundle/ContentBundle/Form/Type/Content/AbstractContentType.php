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
use DTL\Component\Content\Form\ContentTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DTL\Component\Content\Form\ContentView;

/**
 * Adds stubb implementation for content type interface contract
 */
abstract class AbstractContentType extends AbstractType implements ContentTypeInterface
{
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setRequired(array(
            'webspace_key',
            'locale',
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->webspace_key = $options['webspace_key'];
        $view->locale = $options['locale'];
    }

    /**
     * {@inheritDoc}
     */
    public function buildContentView(ContentView $view, FormInterface $form)
    {
    }
}
