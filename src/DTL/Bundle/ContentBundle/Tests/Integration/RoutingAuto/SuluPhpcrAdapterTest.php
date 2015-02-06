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
class SuluPhpcrAdapter extends SuluTestCase
{
    private $content;

    public function setUp()
    {
        $this->autoRouteManager = $this->getContainer()->get('cmf_routing_auto.auto_route_manager');
        $this->session = $this->getContainer()->get('doctrine_phpcr.session');
        $this->requestAnaluzer = $this->getContainer()->get('sulu_core.webspace.request_analyzer.admin');
        $this->initPhpcr();

        $contentParent = $this->session->getNode('/cmf/sulu_io/contents');
        $content = $contentParent->addNode('animals');
        $content->addMixin('sulu:content');
        $this->content = $content;
        $this->session->save();

        $this->webspace = 'sulu_io';
        $this->locale = 'de';

        $request = new Request();
        $request->query->set('webspace', $this->webspace);
        $request->query->set('language', $this->locale);

        $this->requestAnaluzer->analyze($request);
    }

    public function provideAdapter()
    {
        return array(
            array(
                'resource/locator',
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

        $expectedPath = sprintf('/cmf/%s/routes/%s%s', $this->webspace, $this->locale, $expectedUrl);
        $this->session->getNode($expectedPath);
    }

    public function testRedirect()
    {
        $this->createUriContexts('this', 'Foo bar');
        $this->session->save();
        $this->createUriContexts('this-is-new', 'Foo bar');
    }

    private function createUriContexts($resourceLocator, $title)
    {
        $page = new PageDocument();
        $page->setResourceLocator($resourceLocator);
        $page->setTitle($title);
        $page->setUuid($this->content->getIdentifier());

        $uriContextCollection = new UriContextCollection($page);
        $this->autoRouteManager->buildUriContextCollection($uriContextCollection);
        $this->autoRouteManager->handleDefunctRoutes();
        $uriContexts = $uriContextCollection->getUriContexts();
        $this->assertCount(1, $uriContexts);

        $this->session->save();

        return reset($uriContexts);
    }
}
