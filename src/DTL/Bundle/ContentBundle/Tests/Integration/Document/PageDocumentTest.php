<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Tests\Integration\Document;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Bundle\ContentBundle\Document\PageDocument;
use Symfony\Cmf\Component\RoutingAuto\UriContextCollection;
use Symfony\Component\HttpFoundation\Request;
use DTL\Component\Content\Document\DocumentInterface;

class PageDocumentTest extends SuluTestCase
{
    public function setUp()
    {
        $this->manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $this->initPhpcr();
        $this->parent = $this->manager->find(null, '/cmf/sulu_io/contents');
    }

    public function provideMapping()
    {
        return array(
            array(
                array(
                    'Title' => 'Foobar',
                    'Locale' => 'de',
                    'StructureType' => 'contact',
                    'ResourceLocator' => '/foo/bar',
                    'ShadowLocaleEnabled' => true,
                    'ShadowLocale' => 'de',
                    'Creator' => 2,
                    'Changer' => 3,
                    'Content' => array(
                        'name' => 'Daniel Leech',
                        'email' => 'daniel@dantleech.com',
                        'telephone' => '00441305100100',
                        'information' => 'Deutsch Informationen',
                    ),
                ),
            ),
            array(
                array(
                    'ShadowLocaleEnabled' => false,
                    'ShadowLocale' => null,
                    'Title' => 'Foobar',
                    'Locale' => 'en',
                    'StructureType' => 'contact',
                    'ResourceLocator' => '/foo/bar',
                    'Creator' => 2,
                    'Changer' => 3,
                    'Content' => array(
                        'smart-content' => array(
                            'tags' => array('one', 'two', 'three'),
                            'sort' => array('direction' => 'asc', 'field' => 'boo'),
                            'source' => 'boo',
                        ),
                        'telephone' => '00441305100100',
                    ),
                ),
            ),
        );
    }

    /**
     * Assert that the fields are correctly mapped and that
     * they persist correctly.
     *
     * Will set the given data on the Document, then save it, then load it
     * and then assert that the loaded document has the same values as the
     * initial data.
     *
     * @param array $data Value map for page document
     *
     * @dataProvider provideMapping
     */
    public function testMapping($data)
    {
        $page = new PageDocument();
        $this->mapPage($page, $data);
        $this->manager->persist($page);
        $this->manager->flush();
        $this->manager->detach($page);

        $document = $this->manager->find(null, $page->getPath());

        foreach ($data as $field => $expectedValue) {
            $getter = 'get' . $field;

            $this->assertEquals(
                $expectedValue,
                $document->{$getter}(),
                sprintf('Field "%s" is correctly mapped', $field)
            );
        }
    }

    /**
     * Assert the persisting the document in different locales
     * works.
     */
    public function testLocalization()
    {
        $calls = $this->provideMapping();
        $page = new PageDocument();

        foreach ($calls as $args) {
            $data = reset($args);
            $this->mapPage($page, $data);
            $this->manager->persist($page);
            $this->manager->bindTranslation($page, $page->getLocale());
        }

        $this->manager->flush();

        $this->markTestIncomplete('No assertions are made here');
    }

    /**
     * Assert that we change the name of the document PHPCR node
     * when a node with the same name already exists
     */
    public function testConflictResolution()
    {
        $page = $this->createPage('foobar');
        $this->manager->persist($page);
        $this->manager->flush();

        $this->assertEquals('foobar', $page->getName());

        $page = $this->createPage('foobar');
        $this->manager->persist($page);
        $this->manager->flush();

        $this->assertEquals('foobar-1', $page->getName());

        $page = $this->createPage('foobar');
        $this->manager->persist($page);
        $this->manager->flush();

        $this->assertEquals('foobar-2', $page->getName());
    }

    /**
     * Create a page document
     *
     * @param string $title
     */
    private function createPage($title)
    {
        $page = new PageDocument();
        $page->setParent($this->parent);
        $page->setTitle($title);
        $page->setLocale('de');
        $page->setResourceLocator('/foo/bar');
        $page->setStructureType('contact');
        $page->setCreator(1);
        $page->setChanger(1);
        $page->setContent(array(
            'email' => 'dan',
        ));

        return $page;
    }

    /**
     * Map an array of data to a Page document
     *
     * @param mixed $page
     * @param mixed $data
     */
    private function mapPage($page, $data)
    {
        $page->setParent($this->parent);

        foreach ($data as $field => $value) {
            $page->{'set' . $field}($value);
        }

        return $page;
    }

    public function testGetEnabledShadowLocales()
    {
        $page = $this->createLocalizedPage('de', array('en', 'fr'));
        $result = $page->getShadowLocales();

        $this->assertEquals(array(
            'en', 'fr',
        ), $result);
    }

    public function testGetRealLocales()
    {
        $page = $this->createLocalizedPage('de', array('en', 'fr'));
        $result = $page->getRealLocales();

        $this->assertEquals(array(
            'de'
        ), $result);
    }

    public function provideGetLocalizationState()
    {
        return array(
            array(
                'de', 'de', array(),
                DocumentInterface::LOCALIZATION_STATE_LOCALIZED,
            ),
            array(
                'de', 'fr', array(),
                DocumentInterface::LOCALIZATION_STATE_GHOST,
            ),
            array(
                'de', 'fr', array('de'),
                DocumentInterface::LOCALIZATION_STATE_SHADOW,
            ),
        );
    }

    /**
     * @dataProvider provideGetLocalizationState
     */
    public function testGetLocalizationState($requestedLocale, $locale, $shadowLocales, $expectedState)
    {
        $page = $this->createLocalizedPage($locale, $shadowLocales);

        $this->assertEquals($expectedState, $page->getLocalizationState());
    }

    private function createLocalizedPage($locale, array $shadowLocales, $loadInLocale = null)
    {
        $page = new PageDocument();
        $page->setTitle('Hello');
        $page->setParent($this->parent);
        $page->setStructureType('contact');
        $page->setResourceLocator('/foo');
        $this->manager->persist($page);
        $this->manager->bindTranslation($page, $locale);

        foreach ($shadowLocales as $locale) {
            $page->setShadowLocale($locale);
            $page->setShadowLocaleEnabled(true);
            $this->manager->bindTranslation($page, $locale);
        }

        $this->manager->flush();
        $this->manager->clear();

        $page = $this->manager->findTranslation(null, '/cmf/sulu_io/contents/hello', $locale, true);
        $this->assertNotNull($page);

        return $page;
    }
}
