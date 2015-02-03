<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

class TextAreaTypeTest extends AbstractContentTypeTestCase
{
    public function getType()
    {
        return 'text_area';
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
        ));

        $this->assertView($form, '
            <textarea id="text_area"
                data-property=\':property\'
                class="form-element preview-update trigger-save-button hide-in-sortmode"
                data-mapper-property="text_area"
                placeholder=""
            />', array(
                ':property' => htmlentities(json_encode(
                    array(
                        'name' => 'text_area',
                        'metadata' => array(
                            'title' => array(
                                'en' => 'Text area',
                            ),
                        ),
                        'mandatory' => false,
                        'multilingual' => true,
                        'minOccurs' => 1,
                        'maxOccurs' => 999,
                        'contentTypeName' => 'text_area',
                        'params' => array(),
                        'tags' => array(
                        ),
                    )
                )),
            )
        );
    }
}
