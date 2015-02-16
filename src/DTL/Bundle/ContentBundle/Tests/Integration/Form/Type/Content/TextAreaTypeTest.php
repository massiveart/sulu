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
            array(
                array(
                    'placeholder' => 'Hello',
                ),
                array(
                    'placeholder' => array(
                        'de' => 'Hello',
                    ),
                ),
            ), 
            array(
                array(
                    'locale' => 'fr',
                    'placeholder' => 'Hello',
                ),
                array(
                    'placeholder' => array(
                        'fr' => 'Hello',
                    ),
                ),
            ), 
            array(
                array(
                    'locale' => 'fr',
                    'placeholder' => array(
                        'de' => 'Willkommen',
                        'fr' => 'Bienvenue',
                    ),
                ),
                array(
                    'placeholder' => array(
                        'de' => 'Willkommen',
                        'fr' => 'Bienvenue',
                    ),
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
