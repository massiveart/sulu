<?php

namespace DTL\Component\Content\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PageType extends AbstractContentType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('state', 'choice', array(
            'choices' => array(
                'published' => 'published',
                'test' => 'test',
            ),
        ));
    }

    public function getName()
    {
        return 'page';
    }
}
