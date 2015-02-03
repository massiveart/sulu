<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

class TextEditorTypeTest extends AbstractContentTypeTestCase
{
    public function getType()
    {
        return 'text_editor';
    }

    public function testContentTypeView()
    {
        $form = $this->createForm(array(
                'locale' => 'de',
                'webspace_key' => 'sulu_io',
                'labels' => array(
                    'en' => 'Text area',
                ),
                'required' => false,
                'god_mode' => true,
        ));

        $this->assertView($form, '
            <textarea id="text_editor"
                data-property=\':property\'
                class="form-element preview-update trigger-save-button hide-in-sortmode"
                data-mapper-property="text_editor"
                placeholder=""
            />', array(
                ':property' => htmlentities(json_encode(
                    array(
                        'name' => 'text_editor',
                        'metadata' => array(
                            'title' => array(
                                'en' => 'Text area',
                            ),
                        ),
                        'mandatory' => false,
                        'multilingual' => true,
                        'minOccurs' => 1,
                        'maxOccurs' => 999,
                        'contentTypeName' => 'text_editor',
                        'params' => array(),
                        'tags' => array(
                        ),
                    )
                )),
            )
        );
    }
}
