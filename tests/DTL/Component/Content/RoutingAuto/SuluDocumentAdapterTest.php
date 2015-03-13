<?php

namespace DTL\Component\Content\RoutingAuto;

use DTL\Component\Content\RoutingAuto\SuluDocumentAdapter;
use Prophecy\PhpUnit\ProphecyTestCase;

class SuluDocumentAdapterTest extends ProphecyTestCase
{
    private $documentManager;
    private $sessionManager;
    private $reflection;

    public function setUp()
    {
        $this->documentManager = $this->prophesize('Doctrine\ODM\PHPCR\DocumentManager');
        $this->sessionManager = $this->prophesize('Sulu\Component\PHPCR\SessionManager\SessionManager');
        $this->adapter = new SuluDocumentAdapter(
            $this->documentManager->reveal(),
            $this->sessionManager->reveal()
        );
        $this->reflection = new \ReflectionClass(SuluDocumentAdapter::class);
    }
}
