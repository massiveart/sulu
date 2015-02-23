<?php

namespace DTL\Component\Content\Property\Registry;

use Symfony\Component\Form\FormRegistryInterface;
use DTL\Component\Content\Property\PropertyTypeRegistryInterface;
use DTL\Component\Content\Property\PropertyTypeInterface;
use Symfony\Component\Form\FormExtensionInterface;

/**
 * Reduces the scope of the FormRegistry to that of a ContentTypeRegistry
 * and ensures the correct types are returned.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class FormPropertyTypeRegistry implements PropertyTypeRegistryInterface
{
    /**
     * @param FormRegistryInterface $formExtension
     */
    public function __construct(FormExtensionInterface $formExtension)
    {
        $this->formExtension = $formExtension;
    }

    /**
     * {@inheritDoc}
     */
    public function getType($type)
    {
        if (!$this->formExtension->hasType($type)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown content type "%s"',
                $type
            ));
        }

        $type = $this->formExtension->getType($type);

        return $type;
    }
}
