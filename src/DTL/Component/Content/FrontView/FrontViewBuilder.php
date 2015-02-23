<?php

namespace DTL\Component\Content\FrontView;

use DTL\Component\Content\Form\ContentView;
use DTL\Component\Content\Structure\Factory\StructureFactoryInterface;
use DTL\Component\Content\Property\PropertyTypeRegistryInterface;
use DTL\Component\Content\Document\DocumentInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use DTL\Component\Content\Property\PropertyTypeInterface;

/**
 * Build ContentView object from Document and Structure data.)
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class FrontViewBuilder
{
    /**
     * @var ContentTypeRegistryInterface
     */
    private $registry;

    /**
     * @var StructureFactoryInterface
     */
    private $structureFactory;

    /**
     * @param StructureFactoryInterface    $structureFactory
     * @param ContentTypeRegistryInterface $registry
     */
    public function __construct(
        StructureFactoryInterface $structureFactory,
        PropertyTypeRegistryInterface $registry
    ) {
        $this->registry = $registry;
        $this->structureFactory = $structureFactory;
    }

    /**
     * Build a front view from a DocumentInterface instance
     *
     * @param DocumentInterface $documnet
     */
    public function buildFor(DocumentInterface $document)
    {
        $structureType = $document->getStructureType();

        if (!$structureType) {
            throw new \RuntimeException(sprintf(
                'Form document at path "%s" does not have an associated form type',
                $document->getPath()
            ));
        }

        $structure = $this->structureFactory->getStructure($document->getDocumentType(), $structureType);

        return $this->buildFromProperties($structure->properties, $document->getContent());
    }

    /**
     * Build a front view from a collection of properties
     *
     * @param mixed $properties
     * @param mixed $content
     */
    public function buildFromProperties(array $properties, $data)
    {
        $frontView = new FrontView();

        $children = array();

        foreach ($properties as $propertyName => $property) {
            $propertyData = null;

            if (isset($data[$propertyName])) {
                $propertyData = $data[$propertyName];
            }

            $childFrontView = $this->buildType($property->type, $propertyData, $property->parameters);
            $children[] = $childFrontView;
        }

        $frontView->setChildren($children);

        return $frontView;
    }

    /**
     * Build a front view for the given property type name
     *
     * @param string $propertyType
     * @param mixed $data
     * @param array $options
     *
     * @return FrontView
     */
    public function buildType($propertyType, $data, $options)
    {
        $frontView = new FrontView();

        $propertyType = $this->registry->getType($propertyType);

        $typeChain = $this->getTypeChain($propertyType);
        $optionsResolver = new OptionsResolver();

        // resolve the options
        foreach ($typeChain as $propertyType) {
            $propertyType->setDefaultOptions($optionsResolver);
        }

        $options = $optionsResolver->resolve($options);

        // resolve the options
        foreach ($typeChain as $propertyType) {
            $propertyType->buildFrontView($frontView, $data, $options);
        }

        return $frontView;
    }

    private function getTypeChain($propertyType, $parentTypes = array())
    {
        // do not build symfony form types
        if ($propertyType instanceof PropertyTypeInterface) {
            $parentTypes[] = $propertyType;
        }

        $parentType = $propertyType->getParent();

        if (!$parentType) {
            return array_reverse($parentTypes);
        }

        $parentType = $this->registry->getType($parentType);
        return $this->getTypeChain($parentType, $parentTypes);
    }
}
