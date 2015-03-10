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
 */
class HomepageDocumentType extends PageDocumentType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        parent::setDefaultOptions($options);
        $options->setDefaults(array(
            'data_class' => 'DTL\Bundle\ContentBundle\Document\HomepageDocument',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'homepage';
    }
}
