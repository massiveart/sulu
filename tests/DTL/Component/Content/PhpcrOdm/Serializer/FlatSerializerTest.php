<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\PhpcrOdm\Serializer;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Component\Content\Structure\Structure;
use DTL\Component\Content\Structure\Property;
use DTL\Component\Content\PhpcrOdm\Serializer\PropertyNameEncoder;

class FlatSerializerTest extends ProphecyTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->structureFactory = $this->prophesize('DTL\Component\Content\Structure\Factory\StructureFactory');
        $this->structure = new Structure();
        $this->document = $this->prophesize('DTL\Component\Content\Document\DocumentInterface');
        $this->document->getDocumentType()->willReturn('page');
        $this->node = $this->prophesize('PHPCR\NodeInterface');
        $this->encoder = new PropertyNameEncoder('i18n', 'cont');

        $this->structureFactory->getStructure('page', 'test')->willReturn(
            $this->structure
        );

        $this->serializer = new FlatSerializer(
            $this->structureFactory->reveal(),
            $this->encoder
        );
    }

    public function provideSerializer()
    {
        return array(
            array(
                'de',
                array(
                    'some_number' => 1234,
                    'animals' => array(
                        'title' => 'Smart content',
                        'sort_method' => 'asc',
                    ),
                    'options' => array(
                        'numbers' => array(
                            'two', 'three'
                        ),
                        'foo' => array(
                            'bar' => 'baz',
                            'boo' => 'bog',
                        ),
                    ),
                ),
                array(
                    'some_number' => array(
                        'localized' => false,
                    ),
                    'animals' => array(
                        'localized' => true,
                    ),
                    'options' => array(
                        'localized' => false,
                    ),
                ),
                array(
                    'cont:some_number' => 1234,
                    'i18n:de-animals' . FlatSerializer::ARRAY_DELIM . 'title' => 'Smart content',
                    'i18n:de-animals' . FlatSerializer::ARRAY_DELIM . 'sort_method' => 'asc',
                    'cont:options' . FlatSerializer::ARRAY_DELIM . 'numbers' . FlatSerializer::ARRAY_DELIM . '0' => 'two',
                    'cont:options' . FlatSerializer::ARRAY_DELIM . 'numbers' . FlatSerializer::ARRAY_DELIM . '1' => 'three',
                    'cont:options' . FlatSerializer::ARRAY_DELIM . 'foo' . FlatSerializer::ARRAY_DELIM . 'bar' => 'baz',
                    'cont:options' . FlatSerializer::ARRAY_DELIM . 'foo' . FlatSerializer::ARRAY_DELIM . 'boo' => 'bog',
                ),
            ),
        );
    }

    /**
     * @param string $locale Locale to use for the document
     * @param array $data Content data which the document will return
     * @param array $propertyMetadatas Metadata for the structure properties
     * @param array $expectedResult Expected result
     *
     * @dataProvider provideSerializer
     */
    public function testSerialize($locale, $data, $propertyMetadatas, $expectedResult)
    {
        $this->document->getStructureType()->willReturn('test');
        $this->document->getPhpcrNode()->willReturn($this->node);
        $this->document->getLocale()->willReturn($locale);
        $this->document->getContent()->willReturn($data);

        $this->loadMetadata($propertyMetadatas);

        foreach ($expectedResult as $propName => $propValue) {
            $this->node->setProperty($propName, $propValue)->shouldBeCalled();
        }

        $this->serializer->serialize($this->document->reveal());
    }

    /**
     * Note that this test uses the same data as testSerialize but swaps the data
     * and expectedResult.
     *
     * @param mixed $locale
     * @param mixed $expectedResult
     * @param mixed $propertyMetadatas
     * @param mixed $data
     *
     * @dataProvider provideSerializer
     */
    public function testDeserialize($locale, $expectedResult, $propertyMetadatas, $data)
    {
        $this->document->getStructureType()->willReturn('test');
        $this->document->getPhpcrNode()->willReturn($this->node);
        $this->document->getLocale()->willReturn($locale);

        $this->loadMetadata($propertyMetadatas);

        $nodeProperties = array();
        foreach ($data as $propName => $propValue) {
            $nodeProperty = $this->prophesize('PHPCR\PropertyInterface');
            $nodeProperty->getValue()->willReturn($propValue);
            $nodeProperties[$propName] = $nodeProperty->reveal();
        }

        $this->node->getProperties()->willReturn($nodeProperties);

        $res = $this->serializer->deserialize($this->document->reveal());

        $this->assertEquals($expectedResult, $res);
    }

    private function loadMetadata($propertyMetadatas)
    {
        foreach ($propertyMetadatas as $propertyName => $propertyMetadata) {
            $property = new Property();
            foreach ($propertyMetadata as $attrName => $attrValue) {
                $property->$attrName = $attrValue;
            }
            $this->structure->properties[$propertyName] = $property;
        }
    }
}
