<?php

namespace DTL\Component\Content\Type;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Component\Content\Type\FormContentTypeRegistry;

class FormContentTypeRegistryTest extends ProphecyTestCase
{
    private $formRegistry;
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->formRegistry = $this->prophesize('Symfony\Component\Form\FormRegistryInterface');
        $this->contentType = $this->prophesize('DTL\Component\Content\Type\ContentTypeInterface');
        $this->nonContentType = $this->prophesize('Symfony\Component\Form\FormTypeInterface');
        $this->registry = new FormContentTypeRegistry($this->formRegistry->reveal());
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
    public function testRegistryNonContentType()
    {
        $this->formRegistry->getType('foo')->willReturn($this->nonContentType->reveal());
        $contentType = $this->registry->getType('foo');
        $this->assertSame($this->contentType->reveal(), $contentType);
    }
}
