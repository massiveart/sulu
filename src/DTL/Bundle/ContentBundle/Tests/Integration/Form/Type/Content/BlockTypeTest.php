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

use DTL\Bundle\ContentBundle\Form\Type\Content\TextAreaType;
use DTL\Bundle\ContentBundle\Form\Type\Content\BlockType;

class BlockTypeTest extends AbstractContentTypeTestCase
{
    public function getType()
    {
        return $this->getContainer()->get('dtl_content.form.type.block');
    }

    /**
     * {@inheritDoc}
     */
    public function provideFormView()
    {
        return array(
            array(
                array(
                ),
                array(
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function provideFrontViewValue()
    {
        return array(
            array(
                array(
                ),
                null,
                null,
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function provideFrontViewAttributes()
    {
        return array(
            array(
                array(
                ),
                array(
                ),
            ),
        );
    }
}
