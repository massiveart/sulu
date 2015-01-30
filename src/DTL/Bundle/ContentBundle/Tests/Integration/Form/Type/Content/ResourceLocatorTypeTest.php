<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

class ResourceLocatorTypeTest extends AbstractContentTypeTestCase
{
    public function testContentTypeView()
    {
        $form = $this->getContainer()->get('form.factory')->createBuilder()
            ->add('resource_locator', 'resource_locator', array(
                'locale' => 'fr',
                'webspace_key' => 'local',
            ))
        ->getForm();

        $view = $form->createView();

        $templating = $this->getContainer()->get('twig');
        $result = $templating->render(__DIR__ . '/views/test.html.twig', array(
            'view' => $view['resource_locator'],
        ));

        $expected = <<<EOT
<div id="resource_locator"
     class="preview-update trigger-save-button"
     data-property='{&quot;name&quot;:&quot;url&quot;,&quot;metadata&quot;:{&quot;title&quot;:{&quot;de&quot;:&quot;Adresse&quot;,&quot;en&quot;:&quot;Resourcelocator&quot;}},&quot;mandatory&quot;:true,&quot;multilingual&quot;:true,&quot;minOccurs&quot;:1,&quot;maxOccurs&quot;:999,&quot;contentTypeName&quot;:&quot;resource_locator&quot;,&quot;params&quot;:[],&quot;tags&quot;:[{&quot;name&quot;:&quot;sulu.rlp&quot;,&quot;priority&quot;:1}]}'
     data-mapper-property="url"
     data-form="true"
     data-type="resourceLocator"
     data-aura-component="resource-locator@sulucontent"
     data-type-instance-name="url"
     data-aura-history-api="/admin/api/nodes/<%=content.id%>/resourcelocators?language=de&webspace=sulu_io"
     data-aura-content-id="<%=options.id%>"
     data-validation-required="true" ></div>
</div>
EOT;

        $this->assertEquals($expected, $result);
    }
}
