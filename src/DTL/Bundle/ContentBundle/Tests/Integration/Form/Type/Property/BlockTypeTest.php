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

use DTL\Bundle\ContentBundle\Form\Type\Content\TextAreaType;
use DTL\Bundle\ContentBundle\Form\Type\Content\BlockType;

class BlockTypeTest extends AbstractPropertyTypeTestCase
{
    public function getPropertyAlias()
    {
        return 'block';
    }

    /**
     * {@inheritDoc}
     */
    public function provideFormView()
    {
        return array(
            array(
                $this->getOptions(),
                array(
                ),
            ),
        );
    }

    public function provideFormSubmit()
    {
        return array(
            array(
                $this->getOptions(),
                array(
                    'type' => 'editor',
                    'block' => array(
                        'title' => 'Foobar',
                        'body' => 'Body body',
                    ),
                ),
                array(
                    'type' => 'editor',
                    'block' => array(
                        'title' => 'Foobar',
                        'body' => 'Body body',
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
                $this->getOptions(),
                array(
                ),
                array(),
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
                $this->getOptions(),
                array(
                ),
            ),
        );
    }

    private function getOptions()
    {
        return array(
            'default_type' => 'editor',
            'label' => array(
                'de' => 'Block editor',
                'en' => 'Block editor',
            ),
            'prototypes' => array(
                'editor' => array(
                    'options' => array(
                        'label' => array(
                            'en' => 'Text editor',
                        ),
                    ),
                    'properties' => array(
                        'title' => array(
                            'type' => 'text_line',
                            'options' => array(
                                'label' => array(
                                    'en' => 'Title',
                                ),
                            ),
                        ),
                        'body' => array(
                            'type' => 'text_line',
                            'options' => array(
                                'label' => array(
                                    'en' => 'Body',
                                ),
                            ),
                        ),
                    ),
                ),
                'title_only' => array(
                    'options' => array(
                        'label' => array(
                            'en' => 'Text editor',
                        ),
                    ),
                    'properties' => array(
                        'title' => array(
                            'type' => 'text_line',
                            'options' => array(
                                'label' => array(
                                    'en' => 'Title',
                                ),
                            ),
                        ),
                        'body' => array(
                            'type' => 'text_line',
                            'options' => array(
                                'label' => array(
                                    'en' => 'Body',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}
