<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

class ResourceLocatorTypeTest extends AbstractContentTypeTestCase
{
    public function getType()
    {
        return 'resource_locator';
    }

    public function testContentTypeView()
    {
        $form = $this->createForm(array(
                'locale' => 'de',
                'webspace_key' => 'sulu_io',
                'labels' => array(
                    'de' => 'Adresse',
                    'en' => 'Resource Locator',
                ),
                'tags' => array(
                    array(
                        'name' => 'sulu.rlp',
                    ),
                ),
        ));

        $this->assertView($form, '
            <div id="resource_locator"
                 class="preview-update trigger-save-button"
                 data-property=\':property\'
                 data-mapper-property="resource_locator"
                 data-form="true"
                 data-type="resourceLocator"
                 data-aura-component="resource-locator@sulucontent"
                 data-type-instance-name="resource_locator"
                 data-aura-history-api="/admin/api/nodes/<%=content.id%>/resourcelocators?language=de&webspace=sulu_io"
                 data-aura-content-id="<%=options.id%>"
                 data-validation-required="true">
            </div>', array(
                ':property' => htmlentities(json_encode(
                    array(
                        'name' => 'resource_locator',
                        'metadata' => array(
                            'title' => array(
                                'de' => 'Adresse',
                                'en' => 'Resource Locator',
                            ),
                        ),
                        'mandatory' => true,
                        'multilingual' => true,
                        'minOccurs' => 1,
                        'maxOccurs' => 999,
                        'contentTypeName' => 'resource_locator',
                        'params' => array(),
                        'tags' => array(
                            array('name' => 'sulu.rlp', 'priority' => 1),
                        ),
                    )
                )),
            )
        );
    }
}
