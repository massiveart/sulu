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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextLineType extends AbstractContentType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        parent::setDefaultOptions($options);
        $options->setDefaults(array(
            'compound' => false,
        ));

    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'text_line';
    }
}
