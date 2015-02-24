<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Property;

use DTL\Bundle\ContentBundle\Form\Type\Content\ResizeableType;

class CollectionTypeTest extends AbstractPropertyTypeTestCase
{
    public function getPropertyAlias()
    {
        return 'collection';
    }

    /**
     * This form type is special and does not tak the usual options
     */
    protected function completeOptions(array $options)
    {
        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function provideFormView()
    {
        return array(
            array(
                array(
                    'type' => 'text_area',
                    'options' => parent::completeOptions(array()),
                    'max_occurs' => 1,
                    'max_occurs' => 1,
                ),
                array(
                    'multiple' => false,
                ),
            ),
            array(
                array(
                    'type' => 'text_area',
                    'options' => parent::completeOptions(array()),
                    'min_occurs' => 1,
                    'max_occurs' => 2,
                ),
                array(
                    'multiple' => true,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function provideFormSubmit()
    {
        return array(
            array(
                array(
                    'type' => 'text_area',
                    'options' => parent::completeOptions(array()),
                    'min_occurs' => 1,
                    'max_occurs' => 2,
                ),
                array('hello'),
                array('hello'),
            ),
            array(
                // when min and max occurs are both 1, then this is a "single" value
                array(
                    'type' => 'text_area',
                    'options' => parent::completeOptions(array()),
                    'min_occurs' => 1,
                    'max_occurs' => 1,
                ),
                'hello',
                'hello',
            )
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
                    'type' => 'text_area',
                    'options' => parent::completeOptions(array()),
                    'max_occurs' => 1,
                    'max_occurs' => 1,
                ),
                'hello',
                'hello',
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
                    'type' => 'text_area',
                    'options' => parent::completeOptions(array()),
                    'max_occurs' => 1,
                    'max_occurs' => 1,
                ),
                array(
                ),
            ),
        );
    }
}
