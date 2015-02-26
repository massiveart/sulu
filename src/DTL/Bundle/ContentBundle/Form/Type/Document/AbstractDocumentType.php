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
use DTL\Component\Content\Document\DocumentInterface;
use DTL\Component\Content\Structure\Form\StructureFormTypeFactory;
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
            'label',
            'structure_name',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('creator', 'number');
        $builder->add('changer', 'number');

        $structureTypeFactory = $this->structureTypeFactory;
        $structureForm = $structureTypeFactory->createBuilder($this->getName(), $options['structure_name'], array());
        $builder->add($structureForm);
    }
}
