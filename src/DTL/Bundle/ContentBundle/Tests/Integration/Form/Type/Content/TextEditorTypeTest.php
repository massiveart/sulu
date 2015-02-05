<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

use DTL\Bundle\ContentBundle\Form\Type\Content\TextEditorType;

class TextEditorTypeTest extends AbstractContentTypeTestCase
{
    public function getType()
    {
        return new TextEditorType();
    }

    /**
     * {@inheritDoc}
     */
    public function provideFormView()
    {
        return array(
            array(
                array(
                    'god_mode' => false,
                    'tables_enabled' => true,
                    'links_enabled' => true,
                    'paste_from_word' => true,
                ),
                array(
                    'god_mode' => false,
                    'tables_enabled' => true,
                    'links_enabled' => true,
                    'paste_from_word' => true,
                ),
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
                'Hello',
                'Hello',
            ),
            array(
                array(
                ),
                null,
                null,
            ),
        );
    }
}
