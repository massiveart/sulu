<?php

namespace DTL\Component\Content\Structure;

use DTL\Component\Content\Structure\ItemTest;

class StructureTest extends ItemTest
{
    protected $structure;
    protected $prop1;
    protected $prop2;
    protected $prop3;
    protected $prop4;
    protected $section;

    public function setUp()
    {
        $this->prop1 = new Property('prop_1_localized');
        $this->prop1->localized = true;

        $this->prop2 = new Property('prop_2');
        $this->prop3 = new Property('prop_3');
        $this->prop3->localized = true;
        $this->prop4 = new Property('prop_4');

        $this->section = new Section('section_1');
        $this->section->addChild($this->prop3);
        $this->section->addChild($this->prop4);

        $this->structure = new Structure();
        $this->structure->addChild($this->prop1);
        $this->structure->addChild($this->prop2);
        $this->structure->addChild($this->section);
    }

    public function testGetProperties()
    {
        $properties = $this->structure->getProperties();
        $this->assertEquals(array(
            'prop_1_localized' => $this->prop1,
            'prop_2' => $this->prop2,
            'prop_3' => $this->prop3,
            'prop_4' => $this->prop4,
        ), $properties);
    }
}
