<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Form\Type\Document;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Bundle\ContentBundle\Document\PageDocument;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Abstract test class for all content types
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PageDocumentType extends AbstractDocumentType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'data_class' => 'DTL\Bundle\ContentBundle\Document\PageDocument',
        ));

        parent::setDefaultOptions($options);
    }
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('resourceLocator', 'text');
        $builder->add('navigationContexts', 'collection', array(
            'type' => 'text',
        ));
        $builder->add('redirectType', 'text');
        $builder->add('lifecycleStage', 'text');

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'page';
    }
}
