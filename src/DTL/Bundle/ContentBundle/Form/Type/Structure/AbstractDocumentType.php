<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Form\Type\Structure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormBuilderInterface;
use DTL\Bundle\ContentBundle\Document\ContentDocument;

abstract class AbstractDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('creator', 'integer');
        $builder->add('changer', 'integer');
        $builder->add('created', 'datetime');
        $builder->add('updated', 'datetime');

        // set the content form type based on the documents content type
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $content = $event->getData();
            $form = $event->getForm();

            if (!$content instanceof ContentDocument) {
                throw new \InvalidArgumentException('Can only bind objects of type ContentDocument to the ContentFormType');
            }

            $form->add('content', $content->getContentType());
        });
    }

    public function getName()
    {
        return 'content';
    }
}
