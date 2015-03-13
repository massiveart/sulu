<?php

namespace DTL\Bundle\ContentBundle\Form\Type\Base;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use DTL\Bundle\ContentBundle\Form\DataTransformer\DocumentToUuidTransformer;

class DocumentObjectType extends AbstractType
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function getName()
    {
        return 'document_object';
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefault('compound', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new DocumentToUuidTransformer($this->documentManager));
    }
}
