<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Form\Type\Document;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Bundle\ContentBundle\Document\PageDocument;

/**
 * Abstract test class for all content types
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PageDocumentType extends AbstractDocumentType
{
    public function getName()
    {
        return 'page';
    }
}
