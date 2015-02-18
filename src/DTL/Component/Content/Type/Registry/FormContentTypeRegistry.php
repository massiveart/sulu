<?php

namespace DTL\Component\Content\Type\Registry;

use Symfony\Component\Form\FormRegistryInterface;
use DTL\Component\Content\Type\ContentTypeRegistryInterface;
use DTL\Component\Content\Type\ContentTypeInterface;

/**
 * Reduces the scope of the FormRegistry to that of a ContentTypeRegistry
 * and ensures the correct types are returned.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class FormContentTypeRegistry implements ContentTypeRegistryInterface
{
    /**
     * @param FormRegistryInterface $formRegistry
     */
    public function __construct(FormRegistryInterface $formRegistry)
    {
        $this->formRegistry = $formRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getType($type)
    {
        $type = $this->formRegistry->getType($type);

        if (!$type instanceof ContentTypeInterface) {
            throw new \RuntimeException(sprintf(
                'Form registry "%s" did not return a form type of ContentTypeInterface.' .
                'The form registry used for Sulu content must only register Sulu ContentTypeInterface types.',
                get_class($this->formRegistry)
            ));
        }

        return $type;
    }
}
