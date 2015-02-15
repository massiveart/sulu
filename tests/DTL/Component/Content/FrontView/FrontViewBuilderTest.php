<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\FrontView;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Component\Content\FrontView\FrontViewBuilder;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Prophecy\Argument;
use DTL\Component\Content\Structure\Structure;
use DTL\Component\Content\Structure\Property;

class FrontViewBuilderTest extends ProphecyTestCase
{
    /**
     * @var FrontView
     */
    private $builder;

    /**
     * @var ContentFormFactoryInterface
     */
    private $structureFactory;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var FormTypeInterface[]
     */
    private $contentTypes;

    private $formChildrenProphets = array();

    public function setUp()
    {
        parent::setUp();

        $this->structureFactory = $this->prophesize('DTL\Component\Content\Structure\StructureFactoryInterface');
        $this->document1 = $this->prophesize('DTL\Component\Content\Document\DocumentInterface');
        $this->contentTypeRegistry = $this->prophesize('DTL\Component\Content\Type\ContentTypeRegistryInterface');

        $children = array();
        $prodigies = array();

        $this->frontChildren = $children;

        $this->structure = new Structure();
        $this->builder = new FrontViewBuilder(
            $this->structureFactory->reveal(),
            $this->contentTypeRegistry->reveal()
        );
    }

    public function provideBuildFor()
    {
        return array(
            array(
                'example',
                array(
                    'one' => 'hello',
                    'two' => 'world',
                ),
                array(
                    'one' => array(
                        'type' => 'text_line',
                    ),
                    'two' => array(
                        'type' => 'text_line',
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider provideBuildFor
     */
    public function testBuildFor($structureType, $data, $structureProperties)
    {
        $contentTypes = array();
        foreach ($structureProperties as $propertyName => $propertyData) {
            $propertyType = $propertyData['type'];

            if (!isset($contentTypes[$propertyType])) {
                $contentTypes[$propertyType] = $this->prophesize('DTL\Component\Content\Type\ContentTypeFrontInterface');
            }

            $contentType = $contentTypes[$propertyType];
            $contentType->buildFrontView(
                Argument::type('DTL\Component\Content\FrontView\FrontView'),
                $data[$propertyName]
            )->shouldBeCalled();
            $contentType->setDefaultFrontOptions(
                Argument::type('Symfony\Component\OptionsResolver\OptionsResolverInterface')
            )->shouldBeCalled();
            $this->contentTypeRegistry->getType($propertyData['type'])->willReturn(
                $contentType->reveal()
            );
        }

        $this->document1->getStructureType()->willReturn($structureType);
        $this->document1->getDocumentType()->willReturn('page');
        $this->document1->getContent()->willReturn($data);
        $this->structureFactory->getStructure('page', $structureType)->willReturn($this->structure);

        foreach ($structureProperties as $name => $propertyData) {
            $property = new Property();
            foreach ($propertyData as $attrName => $attrValue) {
                $property->$attrName = $attrValue;
            }
            $this->structure->properties[$name] = $property;
        }

        $this->builder->buildFor(
            $this->document1->reveal()
        );
    }
}
