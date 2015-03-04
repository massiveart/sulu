<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\PhpcrOdm;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Component\Content\PhpcrOdm\Serializer\PropertyNameEncoder;

class DocumentNameHelperTest extends ProphecyTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->namespaceRegistry = $this->prophesize('DTL\Component\Content\PhpcrOdm\NamespaceRoleRegistry');
        $this->helper = new DocumentNodeHelper(
            $this->namespaceRegistry->reveal()
        );

        $this->namespaceRegistry->getAlias('localized-content')->willReturn('lcon');
        $this->namespaceRegistry->getAlias('localized-system')->willReturn('lsys');
        $this->namespaceRegistry->getAlias('content')->willReturn('ncon');
    }

    public function testEncodeLocalized()
    {
        $res = $this->helper->encodeLocalizedContentName('prop', 'de');
        $this->assertEquals('lcon:de-prop', $res);
    }

    public function testEncode()
    {
        $res = $this->helper->encodeContentName('prop');
        $this->assertEquals('ncon:prop', $res);
    }

    public function provideGetLocalesForPropertyName()
    {
        return array(
            array(
                array(
                    'lcon:de-foobar',
                    'lcon:de-barbar',
                    'lcon:fr-barbar',
                    'lcon:de_at-barbar',
                    'lcon:fr-barfoo',
                ),
                'barbar',
                array(
                    'de', 'fr', 'de_at',
                ),
            ),
            array(
                array(
                    'lcon:de-foobar',
                    'lcon:de-barbar',
                    'lcon:fr-barbar',
                    'lcon:fr-barfoo',
                ),
                'foobar',
                array(
                    'de'
                ),
            ),
            array(
                array(
                ),
                'foobar',
                array(
                ),
            ),
        );
    }

    /**
     * @dataProvider provideGetLocalesForPropertyName
     */
    public function testGetLocalesForPropertyName($propertyNames, $name, array $expectedLocales)
    {
        $this->node = $this->prophesize('PHPCR\NodeInterface');
        $properties = array();

        foreach ($propertyNames as $propertyName) {
            $property = $this->prophesize('PHPCR\PropertyInterface');
            $property->getName()->willReturn($propertyName);
            $properties[$propertyName] = $property->reveal();
        }

        $this->node->getProperties('lcon:*')->willReturn($properties);

        $locales = $this->helper->getLocalesForPropertyName(
            $this->node->reveal(),
            $name,
            'localized-content'
        );

        $this->assertEquals($expectedLocales, $locales);
    }
}

