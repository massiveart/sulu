<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Bundle\ContentBundle\Document\PageDocument;
use Symfony\Cmf\Component\RoutingAuto\UriContextCollection;
use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract test class for all content types
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class SuluPhpcrAdapterTest extends SuluTestCase
{
    private $content;
    private $documentManager;

    public function setUp()
    {
        $this->autoRouteManager = $this->getContainer()->get('cmf_routing_auto.auto_route_manager');
        $this->manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $this->initPhpcr();
        $this->parent = $this->manager->find(null, '/cmf/sulu_io/contents');
    }

    public function provideAdapter()
    {
        return array(
            array(
                '/resource/locator',
                'This is my title',
                '/resource/locator/this-is-my-title',
            ),
        );
    }

    /**
     * @dataProvider provideAdapter
     */
    public function testAdapter($resourceLocator, $title, $expectedUrl)
    {
        $uriContext = $this->createUriContexts($resourceLocator, $title);
        $this->assertEquals($expectedUrl, $uriContext->getUri());
    }

    public function testRedirect()
    {
        $this->createUriContexts('/this', 'Foo bar');
        $this->createUriContexts('/this-is-new', 'Foo bar');
    }

    private function createUriContexts($resourceLocator, $title)
    {
        $page = new PageDocument();
        $page->setParent($this->parent);
        $page->setResourceLocator($resourceLocator);
        $page->setStructureType('contact');
        $page->setCreator(1);
        $page->setChanger(1);
        $page->setTitle($title);
        $page->setLocale('de');
        $this->manager->persist($page);
        $this->manager->flush();

        $page = $this->manager->find(null, $page->getPath());
        $uriContextCollection = new UriContextCollection($page);
        $this->autoRouteManager->buildUriContextCollection($uriContextCollection);
        $uriContexts = $uriContextCollection->getUriContexts();
        $this->assertCount(1, $uriContexts);

        return reset($uriContexts);
    }
}
