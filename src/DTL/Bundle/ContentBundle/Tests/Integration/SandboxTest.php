<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use DTL\Bundle\ContentBundle\Document\Page;
use DTL\Bundle\ContentBundle\Tests\Resources\Types\OverviewType;
use PHPCR\Util\NodeHelper;
use DTL\Bundle\ContentBundle\Document\PageDocument;

class SandboxTest extends SuluTestCase
{
    public function setUp()
    {
        $this->dm = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $parentPath = '/cmf/sulu_io/contents';
        $node = NodeHelper::createPath($this->dm->getPhpcrSession(), $parentPath);

        foreach ($node->getNodes() as $node) {
            $node->remove();
        }
        $this->dm->getPhpcrSession()->save();

        $parent = $this->dm->find(null, $parentPath);
        $this->createDocuments($parent);
    }

    private function createDocuments($parent)
    {
        $page1 = new PageDocument();
        $page1->setName('page');
        $page1->setParent($parent);
        $page1->setTitle('Gastronomy');
        $page1->setContentType('overview');
        $page1->setContent(array(
            'title' => 'bar',
            'url' => 'http://foobar',
        ));

        $this->dm->persist($page1);
        $this->dm->flush();
        $this->page1 = $page1;
    }

    public function testContentView()
    {
        $page = $this->dm->find(null, '/cmf/sulu_io/contents/page');

        $resolver = $this->getContainer()->get('dtl_content.form.content_view_resolver');
        $resolver->resolve($page);
    }

    public function testPage()
    {
        $page = $this->dm->find(null, '/cmf/sulu_io/contents/page');

        $factory = $this->container->get('form.factory');
        $form = $factory->create('page', $page);

        $data = array(
            'title' => 'boo',
            'state' => 'published',
            'content' => array(
                'article' => 'Hello this is article',
                'contactEmail' => 'daniel@dantleech.com',
            ),
        );

        $form->submit($data);

        $data = $page->getContent();

        $this->dm->persist($page);
        $this->dm->flush();
    }
}
