<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Compat;

use Sulu\Component\Content\Mapper\ContentMapperRequest;
use Sulu\Component\Content\StructureInterface;
use DTL\Bundle\ContentBundle\Tests\Integration\BaseTestCase;
use PHPCR\Util\PathHelper;
use DTL\Bundle\ContentBundle\Document\PageDocument;

class ContentMapper_loadTest extends BaseTestCase
{
    private $contentMapper;

    public function setUp()
    {
        $this->initPhpcr();
        $this->contentMapper = $this->getContainer()->get('dtl_content.compat.content_mapper');
        $this->manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
    }

    public function provideLoad()
    {
        return array(
            array(
                'fr', 'de', false, false, false,
                false,
            ),
            array(
                'fr', 'de', true, false, true,
                true,
            ),
        );
    }
    
    /**
     * @dataProvider provideLoad
     *
     * @param mixed $excludeGhost
     * @param mixed $loadGhostContent
     * @param mixed $excludeShadow
     */
    public function testLoadByNode($locale, $requestLocale, $excludeGhost = true, $loadGhostContent = false, $excludeShadow = true, $shouldBeNull)
    {
        $locale = 'fr';
        $document = $this->createDocument('/cmf/sulu_io/contents/node', $locale);
        $this->manager->flush();

        $result = $this->contentMapper->loadByNode(
            $document->getPhpcrNode(),
            $requestLocale,
            'sulu_io',
            $excludeGhost,
            $loadGhostContent,
            $excludeShadow
        );

        if ($shouldBeNull) {
            $this->assertNull($result);
            return;
        }

        $this->assertInstanceOf('DTL\Component\Content\Compat\Structure\StructureBridge', $result);
    }

    /**
     * Data provider
     */
    public function provideLoadByParent()
    {
        return array(
            array(
                array('child1', 'child2'),
                1, false, false, false,
                array(
                    'child1', 'child2'
                ),
            ),
            array(
                array('child1', 'child2', 'child2/child3'),
                2, true, false, false,
                array(
                    'child1', 'child2', 'child2/child3',
                ),
            ),
            array(
                array('child1', 'child2', 'child2/child3', 'child2/child3/child4'),
                4, true, false, false,
                array(
                    'child1', 'child2', 'child2/child3', 'child2/child3/child4',
                ),
            ),
            array(
                array('child1', 'child2', 'child2/child3'),
                1, true, false, false,
                array(
                    'child1', 'child2',
                ),
            ),
        );
    }

    /**
     * @dataProvider provideLoadByParent
     *
     * Load children for the given parent node
     *
     * @param mixed $children Names of the children to create
     * @param mixed $depth Depth to fetch (or prefetch)
     * @param mixed $flat Return as a flat list (not nested)
     * @param mixed $ignoreExceptions 
     * @param mixed $excludeGhosts
     * @param array $expected
     */
    public function testLoadByParent($children, $depth, $flat, $ignoreExceptions, $excludeGhosts, array $expected)
    {
        $locale = 'fr';
        $parent = $this->createDocument('/cmf/sulu_io/contents/parent', 'Parent');
        foreach ($children as $childPath) {
            $this->createDocument('/cmf/sulu_io/contents/parent/' . $childPath, $locale);
        }
        $this->manager->flush();

        $result = $this->contentMapper->loadByParent(
            $parent->getUuid(),
            'sulu_io',
            $locale,
            $depth,
            $flat,
            $ignoreExceptions,
            $excludeGhosts
        );

        $paths = array();
        foreach ($result as $document) {
            $paths[] = substr($document->getPath(), strlen($parent->getPath()) + 1);
        }

        $this->assertEquals($expected, $paths);
    }

    public function testLoadStartPage()
    {
        $startPage = $this->contentMapper->loadStartPage('sulu_io', 'de');
        $this->assertEquals('/cmf/sulu_io/contents', $startPage->getPath());
    }

    public function testLoadByResourceLocator()
    {
        $this->contentMapper->loadByResourceLocator('/', 'sulu_io', 'de');
    }

    public function testLoadBySql2()
    {
        $this->markTestSkipped('todo');
    }

    public function testLoadByQuery()
    {
        $this->markTestSkipped('todo');
    }

    public function testLoadTreeByUuid()
    {
        $this->markTestSkipped('todo');
    }

    public function testLoadTreeByPath()
    {
        $this->markTestSkipped('todo');
    }

    public function testLoadBreadcrumb()
    {
        $this->markTestSkipped('todo');
    }

    private function createDocument($path, $locale)
    {
        $parent = $this->manager->find(null, PathHelper::getParentPath($path));
        $name = PathHelper::getNodeName($path);

        if (null === $parent) {
            throw new \InvalidArgumentException('Cannot find parent: ' . $path);
        }

        $document = new PageDocument();
        $document->setTitle($name);
        $document->setParent($parent);
        $document->setStructureType('contact');
        $document->setResourceLocator('/' . $name);
        $document->setLocale('fr');

        $this->manager->persist($document);

        return $document;
    }
}
