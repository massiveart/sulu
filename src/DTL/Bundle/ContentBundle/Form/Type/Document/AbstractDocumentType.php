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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormBuilderInterface;
use DTL\Component\Content\Document\DocumentInterface;
use DTL\Component\Content\Structure\Form\StructureFormTypeFactory;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use Sulu\Component\PHPCR\SessionManager\SessionManagerInterface;

abstract class AbstractDocumentType extends AbstractType
{
    /**
     * @var StructureFormTypeFactory
     */
    private $structureTypeFactory;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @param StructureFormTypeFactory $structureTypeFactory
     * @param SessionManagerInterface $sessionManager
     * @param DocumentManager $documentManager
     */
    public function __construct(
        StructureFormTypeFactory $structureTypeFactory,
        SessionManagerInterface $sessionManager,
        DocumentManager $documentManager
    )
    {
        $this->structureTypeFactory = $structureTypeFactory;
        $this->sessionManager = $sessionManager;
        $this->documentManager = $documentManager;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setRequired(array(
            'webspace_key',
            'locale',
            'label',
            'structure_name',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('parent', 'document_object');
        $builder->setAttribute('webspace_key', $options['webspace_key']);
        $builder->setAttribute('structure_name', $options['structure_name']);
        $builder->setAttribute('locale', $options['locale']);

        $structureTypeFactory = $this->structureTypeFactory;
        $structureForm = $structureTypeFactory->createBuilder($this->getName(), $options['structure_name'], array());

        $builder->add($structureForm);
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'postSubmitDocumentParent'));
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'postSubmitStructureName'));
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'postSubmitLocale'));
    }

    /**
     * Set the document parent to be the webspace content path
     * when the document has no parent.
     *
     * @param FormEvent $event
     */
    public function postSubmitDocumentParent(FormEvent $event)
    {
        $document = $event->getData();

        if ($document->getParent()) {
            return;
        }

        $form = $event->getForm();
        $webspaceKey = $form->getConfig()->getAttribute('webspace_key');
        $parent = $this->documentManager->find(null, $this->sessionManager->getContentPath($webspaceKey));

        if (null === $parent) {
            throw new \InvalidArgumentException(sprintf(
                'Could not determine parent for document with title "%s" in webspace "%s"',
                $document->getTitle(),
                $webspaceKey
            ));
        }

        $document->setParent($parent);
    }

    /**
     * Assign the name of the structure to the document
     *
     * @param FormEvent $event
     */
    public function postSubmitStructureName(FormEvent $event)
    {
        $document = $event->getData();
        $documentStructureName = $document->getStructureType();
        $structureName = $event->getForm()->getConfig()->getAttribute('structure_name');
        $document->setStructureType($structureName);
    }

    /**
     * Assign the current locale to the document
     *
     * @param FormEvent $event
     */
    public function postSubmitLocale(FormEvent $event)
    {
        $locale = $event->getForm()->getConfig()->getAttribute('locale');
        $event->getData()->setLocale($locale);
    }
}
