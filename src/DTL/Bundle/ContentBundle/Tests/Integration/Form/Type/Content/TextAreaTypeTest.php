<?php

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
    public function provideContentView()
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
