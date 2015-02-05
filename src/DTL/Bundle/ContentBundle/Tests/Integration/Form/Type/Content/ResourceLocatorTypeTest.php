<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

use DTL\Bundle\ContentBundle\Form\Type\Content\ResourceLocatorType;

class ResourceLocatorTypeTest extends AbstractContentTypeTestCase
{
    public function getType()
    {
        return new ResourceLocatorType();
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
