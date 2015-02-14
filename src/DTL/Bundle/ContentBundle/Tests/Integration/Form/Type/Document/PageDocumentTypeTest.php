<?php
/**
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Document;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Bundle\ContentBundle\Document\PageDocument;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PageDocumentTypeTest extends SuluTestCase
{
    private $factory;

    public function setUp()
    {
        $this->factory = $this->getContainer()->get('form.factory');
    }

    public function testType()
    {
        $page = new PageDocument();
        $page->setStructureType('contact');

        $form = $this->factory->create('page', $page, array(
            'webspace_key' => 'sulu_io',
            'locale' => 'de',
        ));

        $view = $form->createView();
    }
}

