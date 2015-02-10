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
    }

    public function provideDocument()
    {
        return array(
            array(
                array(
                    'Path' => '/cmf/sulu_io/contents/foobar',
                    'Title' => 'Foobar',
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
        );
    }

    /**
     * @dataProvider provideDocument
     */
    public function testDocument($data)
    {
        $page = new PageDocument();

        foreach ($data as $field => $value) {
            $page->{'set' . $field}($value);
        }

        $this->manager->persist($page);
        $this->manager->flush();
        $this->manager->detach($page);

        $document = $this->manager->findOneByTitle($data['Title']);
        foreach ($data as $field => $expectedValue) {
            $this->assertEqulals(
                $expectedValue,
                $page->{'get' . $field}()
            );
        }
    }
}
