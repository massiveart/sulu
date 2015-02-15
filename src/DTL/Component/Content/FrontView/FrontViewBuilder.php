<?php

namespace DTL\Component\Content\FrontView;

use DTL\Component\Content\Form\ContentView;
use DTL\Component\Content\Structure\StructureFactoryInterface;
use DTL\Component\Content\Type\ContentTypeRegistryInterface;
use DTL\Component\Content\Document\DocumentInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Build ContentView object from Document and Structure data.
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
        ContentTypeRegistryInterface $registry
    ) {
        $this->registry = $registry;
        $this->structureFactory = $structureFactory;
    }

    /**
     * Resolve the given structure document into a content view
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

        $frontView = new FrontView();
        $contentData = $document->getContent();

        $children = array();

        foreach ($structure->properties as $propertyName => $property) {
            $propertyData = null;

            if (isset($contentData[$propertyName])) {
                $propertyData = $contentData[$propertyName];
            }

            $childFrontView = new FrontView();
            $contentType = $this->registry->getType($property->type);

            // resolve the options
            $optionsResolver = new OptionsResolver();
            $contentType->setDefaultFrontOptions($optionsResolver);
            $optionsResolver->resolve($property->frontOptions);

            $contentType->buildFrontView($childFrontView, $propertyData);
        }

        $frontView->setChildren($children);

        return $frontView;
    }
}
