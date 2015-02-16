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

class TextAreaTypeTest extends AbstractContentTypeTestCase
{
    public function getType()
    {
        return new TextAreaType();
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
    public function provideContentViewValue()
    {
        return array(
            array(
                array(
                ),
                null,
                null,
            ),
            array(
                array(
                ),
                'This is some text in my area',
                'This is some text in my area',
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function provideContentViewAttributes()
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
