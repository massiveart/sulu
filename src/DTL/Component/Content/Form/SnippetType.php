<?php

namespace DTL\Component\Content\Form\Extension\Type;

use Symfony\Component\Form\AbstractType;

class SnippetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function getParent()
    {
        return 'content';
    }

    public function getName()
    {
        return 'snippet';
    }
}
