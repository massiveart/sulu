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
                    'StructureType' => 'overview',
                    'ResourceLocator' => 'foo/bar',
                    'Creator' => 2,
                    'Changer' => 3,
                    'Created' => new \DateTime('2015-02-09'),
                    'Changed' => new \DateTime('2015-02-09'),
                    'Content' => array(
                        'email' => 'daniel@dantleech.com',
                        'telephone' => '00441305100100',
                    ),
                ),
            ),
            array(
                array(
                    'Title' => 'Foobar',
                    'Locale' => 'en',
                    'StructureType' => 'overview',
                    'ResourceLocator' => 'foo/bar',
                    'Creator' => 2,
                    'Changer' => 3,
                    'Created' => new \DateTime('2015-02-09'),
                    'Changed' => new \DateTime('2015-02-09'),
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
     * @param array $data Value map for page document
     *
     * @dataProvider provideMapping
     */
    public function testMapping($data)
    {
        $page = new PageDocument();
        $page->setParent($this->parent);

        foreach ($data as $field => $value) {
            $page->{'set' . $field}($value);
        }

        $this->manager->persist($page);
        $this->manager->flush();
        $this->manager->detach($page);

        $document = $this->manager->find(null, $page->getPath());

        foreach ($data as $field => $expectedValue) {
            $this->assertEquals(
                $expectedValue,
                $document->{'get' . $field}()
            );
        }
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
        $page->setResourceLocator('foo/bar');
        $page->setStructureType('overview');
        $page->setCreator(1);
        $page->setChanger(1);
        $page->setCreated(new \DateTime());
        $page->setChanged(new \DateTime());
        $page->setContent(array(
            'foo' => array(
                'bar' => 'foo',
            ),
            'bar' => array(
                'baz' => 'god',
            ),
        ));

        return $page;
    }
}
