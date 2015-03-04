<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\PhpcrOdm\EventSubscriber;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Component\Content\PhpcrOdm\EventSubscriber\NamespaceRegistrySubscriber;

class NamespaceRegistrySubscriberTest extends ProphecyTestCase
{
    private $subscriber;
    private $registry;
    private $document;

    public function setUp()
    {
        $this->registry = $this->prophesize('DTL\Component\Content\PhpcrOdm\NamespaceRoleRegistry');
        $this->document = $this->prophesize('DTL\Component\Content\Document\DocumentInterface');
        $this->event = $this->prophesize('Doctrine\ORM\Event\LifecycleEventArgs');
        $this->subscriber = new NamespaceRegistrySubscriber($this->registry->reveal());
    }

    public function testNotDocument()
    {
        $this->document->setNamespaceRegistry()->shouldNotBeCalled();
        $this->event->getObject()->willReturn(new \stdClass);
        $this->subscriber->postLoad($this->event->reveal());
    }

    public function testSetNamepsaceRegistry()
    {
        $this->document->setNamespaceRegistry($this->registry->reveal())->shouldBeCalled();
        $this->event->getObject()->willReturn($this->document->reveal());
        $this->subscriber->postLoad($this->event->reveal());
    }
}

