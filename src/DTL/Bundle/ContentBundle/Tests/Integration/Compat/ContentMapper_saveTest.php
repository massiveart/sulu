<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Compat;

use Sulu\Component\Content\Mapper\ContentMapperRequest;
use Sulu\Component\Content\StructureInterface;
use DTL\Bundle\ContentBundle\Tests\Integration\BaseTestCase;

class ContentMapper_saveTest extends BaseTestCase
{
    private $contentMapper;

    public function setUp()
    {
        $this->initPhpcr();
        $this->parent = $this->getDm()->find(null, '/cmf/sulu_io/contents');
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

    /**
     * Updates the node with a different language
     */
    public function testSaveUpdate()
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

    public function testSaveStartPage()
    {
        // this is actually the data sent for mapping ...
        $data = json_decode('
            {
                "_embedded": {
                    "nodes": []
                },
                "_links": {
                    "children": "/admin/api/nodes?parent=8f817a80-d48f-4181-9319-96ecc9ad33b6&depth=1&webspace=sulu_io&language=de",
                    "self": "/admin/api/nodes/8f817a80-d48f-4181-9319-96ecc9ad33b6"
                },
                "changed": "2015-03-10T13:59:16+0100",
                "concreteLanguages": [
                    "en",
                    "de"
                ],
                "created": "2015-03-10T13:59:16+0100",
                "enabledShadowLanguages": [],
                "hasSub": false,
                "id": "index",
                "internal": false,
                "navigation": true,
                "nodeState": 2,
                "nodeType": 1,
                "originTemplate": "prototype",
                "path": "/cmf/sulu_io/contents",
                "published": "2015-03-10T13:59:16+0100",
                "shadowBaseLanguage": false,
                "shadowOn": false,
                "template": "contact",
                "title": "asdHomepage",
                "url": "/"
            }
        ', true);

        $this->contentMapper->saveStartPage(
            $data,
            'contact',
            'sulu_io',
            'en',
            1
        );
    }
}
