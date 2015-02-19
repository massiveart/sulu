<?php

namespace DTL\Component\Property\Type\Registry;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Component\Property\Type\Registry\FormPropertyTypeRegistry;

class FormPropertyTypeRegistryTest extends ProphecyTestCase
{
    private $formRegistry;
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->formRegistry = $this->prophesize('Symfony\Component\Form\FormRegistryInterface');
        $this->contentType = $this->prophesize('DTL\Component\Property\Type\PropertyTypeInterface');
        $this->nonPropertyType = $this->prophesize('Symfony\Component\Form\FormTypeInterface');
        $this->registry = new FormPropertyTypeRegistry($this->formRegistry->reveal());
    }

    /**
     * Ensure content type repository returns content types
     */
    public function testRegistry()
    {
        $this->formRegistry->getType('foo')->willReturn($this->contentType->reveal());
        $contentType = $this->registry->getType('foo');
        $this->assertSame($this->contentType->reveal(), $contentType);
    }

    /**
     * Ensure an exception is thrown when a non content type is returned
     *
     * @expectedException RuntimeException
     */
    public function testRegistryNonPropertyType()
    {
        $this->formRegistry->getType('foo')->willReturn($this->nonPropertyType->reveal());
        $contentType = $this->registry->getType('foo');
        $this->assertSame($this->contentType->reveal(), $contentType);
    }
}
