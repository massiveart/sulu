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
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class TextAreaType extends AbstractContentType
{
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        parent::setDefaultOptions($options);

        $options->setDefaults(array(
            'placeholder' => '',
        ));

        $options->setAllowedTypes(array(
            'placeholder' => array('string', 'array')
        ));

        $options->setNormalizers(array(
            'placeholder' => function ($options, $value) {
                if (!is_array($value)) {
                    return array(
                        $options['locale'] => $value
                    );
                }

                return $value;
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['placeholder'] = $options['placeholder'];
    }

    public function getName()
    {
        return 'text_area';
    }
}
