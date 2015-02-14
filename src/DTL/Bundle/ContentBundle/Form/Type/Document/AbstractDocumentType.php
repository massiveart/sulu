<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Form\Type\Document;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormBuilderInterface;
use DTL\Component\Content\Model\DocumentInterface;
use DTL\Component\Content\Form\Factory\StructureFormTypeFactory;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractDocumentType extends AbstractType
{
    private $structureTypeFactory;

    public function __construct(StructureFormTypeFactory $structureTypeFactory)
    {
        $this->structureTypeFactory = $structureTypeFactory;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setRequired(array(
            'webspace_key',
            'locale',
            'labels',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('creator', 'integer');
        $builder->add('changer', 'integer');
        $builder->add('created', 'datetime');
        $builder->add('changed', 'datetime');

        $structureTypeFactory = $this->structureTypeFactory;

        // set the document form type based on the documents document type
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($structureTypeFactory, $options) {
            $document = $event->getData();

            if (!$document) {
                return;
            }

            $form = $event->getForm();

            if (!$document instanceof DocumentInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'Can only bind objects of type DocumentInterface to "%s", got "%s"',
                    get_class($this),
                    is_object($document) ? get_class($document) : gettype($document)
                ));
            }

            $structureForm = $structureTypeFactory->createFor($document, $options);
            $form->add($structureForm);
        });
    }
}
