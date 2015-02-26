<?php

namespace DTL\Component\Content\Compat\Structure;

use DTL\Component\Content\Structure\Structure;
use DTL\Component\Content\Structure\Property;
use DTL\Component\Content\Compat\Structure\StructureBridge;

class StructureBridgeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $structure = new Structure();
        $structure->name = 'test';
        $structure->title = array('de' => 'Beispiel', 'en' => 'Example');
        $structure->description = array('de' => 'Beschreibung', 'en' => 'Description');
        $structure->tags = array(
            array(
                'name' => 'tag1',
            ),
        );
        $structure->parameters = array(
            'one' => 'one_value',
            'two' => 'two_value',
        );
        $structure->properties['prop_1'] = new Property();
        $structure->properties['prop_1']->name = 'title';
        $structure->properties['prop_1']->localized = false;
        $structure->properties['prop_1']->type = 'text_line';
        $structure->properties['prop_1']->title = array('en' => 'Property One', 'de' => 'Eigenschaft eins');
        $structure->properties['prop_1']->colSpan = 3;
        $structure->properties['prop_1']->cssClass = 'blue-moon';
        $structure->properties['prop_2'] = new Property();
        $structure->properties['prop_2']->type = 'text_line';
        $structure->properties['prop_2']->localized = false;
        $structure->properties['prop_2']->colSpan = 3;
        $structure->properties['prop_2']->cssClass = 'blue-moon';

        $this->structure = $structure;
        $this->bridge = new StructureBridge($this->structure);
    }

    public function provideGet()
    {
        return array(
            array('getKey', 'test'),
        );
    }

    /**
     * Should return the value of structure.name
     */
    public function testGetKey()
    {
        $this->assertEquals(
            'test',
            $this->bridge->getKey()
        );
    }

    public function testGetProperties()
    {
        $properties = $this->bridge->getProperties();
        $this->assertCount(2, $properties);
    }

    public function testGetPropertyNames()
    {
        $this->assertEquals(
            array('prop_1', 'prop_2'),
            $this->bridge->getPropertyNames()
        );
    }

    public function testGetProperty()
    {
        $property = $this->bridge->getProperty('prop_1');
        $this->assertNotNull($property);

        $map = array(
            'isMandatory' => 'required',
            'isMultilingual' => 'localized',
            'getMinOccurs' => 'minOccurs',
            'getMaxOccurs' => 'maxOccurs',
            'getColspan' => 'colSpan',
            'getParams' => 'parameters',
        );

        foreach ($map as $method => $propName) {
            $this->assertEquals(
                $this->bridge->getProperty('prop_1')->$method($propName),
                $this->structure->properties['prop_1']->$propName
            );
        }
    }

    public function testGetPropertyTitle()
    {
        $property = $this->bridge->getProperty('prop_1');
        $this->assertEquals('Eigenschaft eins', $property->getTitle('de'));

    }

    public function testGetLocalizedTitle()
    {
        $this->assertEquals(
            'Beispiel',
            $this->bridge->getLocalizedTitle('de')
        );
    }

    public function testStructureTags()
    {
        $this->markTestIncomplete('Write this test');
    }

    public function testPropertyTags()
    {
        $this->markTestIncomplete('Write this test');
    }
}
