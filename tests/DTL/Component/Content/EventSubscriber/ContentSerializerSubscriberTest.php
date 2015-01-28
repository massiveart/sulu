<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Serializer;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Component\Content\EventSubscriber\ContentSerializerSubscriber;
use Prophecy\Argument;

class ContentSerializerSubscriberTest extends ProphecyTestCase
{
    private $documentManager;
    private $subscriber;

    public function setUp()
    {
        parent::setUp();

        $this->documentManager = $this->prophesize('Doctrine\ODM\PHPCR\DocumentManager');
        $this->serializer = $this->prophesize('DTL\Component\Content\Serializer\SerializerInterface');
        $this->content = $this->prophesize('DTL\Component\Content\Model\ContentInterface');
        $this->node = $this->prophesize('PHPCR\NodeInterface');
        $this->notContent = new \stdClass;

        $this->lifecycleEventArgs = $this->prophesize('Doctrine\Common\Persistence\Event\LifecycleEventArgs');
        $this->managerEventArgs = $this->prophesize('Doctrine\Common\Persistence\Event\ManagerEventArgs');
        $this->phpcrSession = $this->prophesize('PHPCR\SessionInterface');

        $this->subscriber = new ContentSerializerSubscriber(
            $this->documentManager->reveal(),
            $this->serializer->reveal()
        );
    }

    public function testPostLoadNoContent()
    {
        $this->lifecycleEventArgs->getObject()->willReturn($this->notContent);
        $this->serializer->deserialize(Argument::any())->shouldNotBeCalled();
        $this->subscriber->postLoad($this->lifecycleEventArgs->reveal());
    }

    public function testPostLoad()
    {
        $this->lifecycleEventArgs->getObject()->willReturn($this->content->reveal());

        $this->documentManager->getNodeForDocument($this->content)->willReturn($this->node);
        $this->serializer->deserialize($this->node)->willReturn(array('foo' => 'bar'));
        $this->content->setContent(array('foo' => 'bar'))->shouldBeCalled();

        $this->subscriber->postLoad($this->lifecycleEventArgs->reveal());
    }

    public function provideUpdate()
    {
        return array(
            array('preUpdate'),
            array('prePersist'),
        );
    }

    /**
     * @dataProvider provideUpdate
     */
    public function testUpdate($method)
    {
        $this->lifecycleEventArgs->getObject()->willReturn($this->content);
        $this->documentManager->getNodeForDocument($this->content)->willReturn($this->node);
        $this->documentManager->getPhpcrSession()->willReturn($this->phpcrSession);
        $this->managerEventArgs->getObjectManager()->willReturn($this->documentManager);
        $this->content->getContent()->willReturn(array('foo' => 'bar'));

        $this->serializer->serialize(array('foo' => 'bar'), $this->node)->shouldBeCalled();

        $this->subscriber->$method($this->lifecycleEventArgs->reveal());
        $this->subscriber->endFlush($this->managerEventArgs->reveal());
    }
}
