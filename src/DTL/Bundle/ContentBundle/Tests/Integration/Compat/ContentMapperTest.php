<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Compat;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Sulu\Component\Content\Mapper\ContentMapperRequest;
use Sulu\Component\Content\StructureInterface;

class ContentMapperTest extends SuluTestCase
{
    public function setUp()
    {
        $this->manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $this->initPhpcr();
        $this->parent = $this->manager->find(null, '/cmf/sulu_io/contents');
        $this->contentMapper = $this->getContainer()->get('dtl_content.compat.content_mapper');
    }

    public function provideSave()
    {
        return array(
            array(
                ContentMapperRequest::create('page')
                    ->setTemplateKey('contact')
                    ->setWebspaceKey('sulu_io')
                    ->setUserId(1)
                    ->setState(StructureInterface::STATE_PUBLISHED)
                    ->setLocale('en')
                    ->setData(array(
                        'title' => 'This is a test',
                        'url' => '/url/to/content',
                        'name' => 'Daniel Leech',
                        'email' => 'daniel@dantleech.com',
                        'telephone' => '123123',
                    ))
            ),
        );
    }

    /**
     * @dataProvider provideSave
     */
    public function testSave($request)
    {
        $this->contentMapper->saveRequest($request);
    }

    public function testSaveMultiple()
    {
        $request = ContentMapperRequest::create('page')
            ->setTemplateKey('contact')
            ->setWebspaceKey('sulu_io')
            ->setUserId(1)
            ->setState(StructureInterface::STATE_PUBLISHED)
            ->setLocale('en')
            ->setData(array(
                'title' => 'This is a test',
                'url' => '/url/to/content',
                'name' => 'Daniel Leech',
                'email' => 'daniel@dantleech.com',
                'telephone' => '123123',
            ));

        $structure = $this->contentMapper->saveRequest($request);

        $request = ContentMapperRequest::create('page')
            ->setTemplateKey('contact')
            ->setWebspaceKey('sulu_io')
            ->setUuid($structure->getUuid())
            ->setUserId(1)
            ->setState(StructureInterface::STATE_PUBLISHED)
            ->setLocale('de')
            ->setData(array(
                'title' => 'Ceci est une test',
                'url' => '/url/to/content',
                'name' => 'Danièl le Français',
                'email' => 'daniel@dantleech.com',
                'telephone' => '123123',
            ));
        $this->contentMapper->saveRequest($request);
    }
}
