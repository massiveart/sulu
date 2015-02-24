<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Structure\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\Form\ResolvedFormType;
use DTL\Component\Content\Structure\Factory\StructureFactory;
use Symfony\Component\Form\FormFactoryInterface;
use DTL\Component\Content\Document\DocumentInterface;

/**
 * Creates forms for structures using the Metadata from
 * StructureMetadataFactory.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class StructureFormTypeFactory
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var StructureFactory
     */
    private $structureFactory;

    /**
     * @param StructureFactory $structureFactory
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(StructureFactory $structureFactory, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->structureFactory = $structureFactory;
    }

    /**
     * create the structure form type
     *
     * @param mixed $documenttype the document type (e.g. page, snippet)
     * @param mixed $structuretype the structure type (e.g. overview, example)
     * @param array $options form options (e.g. webspace, locale)
     */
    public function create($documentType, $structureType, array $options)
    {
        $structure = $this->structureFactory->getStructure($documentType, $structureType);

        $builder = $this->formFactory->createNamedBuilder('content', 'form', null, array(
            'auto_initialize' => false, // auto initialize should only be for root nodes
        ));

        foreach ($structure->properties as $name => $property) {
            $builder->add($name, 'collection', array(
                'type' => $property->type,
                'options' => $property->parameters,
                'label' => $property->title,
                'min_occurs' => $property->minOccurs,
                'max_occurs' => $property->maxOccurs,
            ));
        }

        return $builder->getForm();
    }

    /**
     * create the structure form type from a document
     *
     * @param DocumentInterface $document
     * @param array $options
     */
    public function createFor(DocumentInterface $document, array $options)
    {
        return $this->create($document->getDocumentType(), $document->getStructureType(), $options);
    }
}
