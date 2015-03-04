<?php

namespace DTL\Component\Content\PhpcrOdm;

use PHPCR\NodeInterface;

/**
 * This class is responsible for generating and inferring
 * things from the PHPCR node.
 */
class DocumentNodeHelper
{
    private $namespaceRegistry;

    /**
     * @param NamespaceRoleRegistry $namespaceRegistry
     */
    public function __construct(NamespaceRoleRegistry $namespaceRegistry)
    {
        $this->namespaceRegistry = $namespaceRegistry;
    }

    /**
     * Return the locales which are associated with the given
     * property name for the given node and role (one of the roles
     * registered with the namespace role registry, e.g. localized-system).
     *
     * @param NodeInterface $node
     * @param string $name
     * @param string $role
     */
    public function getLocalesForPropertyName(NodeInterface $node, $name, $role)
    {
        $locales = array();
        $namespaceAlias = $this->namespaceRegistry->getAlias($role);

        $properties = $node->getProperties(sprintf(
            '%s:*', $namespaceAlias
        ));

        foreach ($properties as $property) {
            if (!preg_match($p = sprintf('{%s:([a-z_A-Z]+)-%s}', $namespaceAlias, $name), $property->getName(), $matches)) {
                continue;
            }

            $locales[] = $matches[1];
        }

        return $locales;
    }

    /**
     * Encode the given property name and localte to a PHPCR
     * property name.
     *
     * @param string $propName
     * @param string $locale
     *
     * @return string
     */
    public function encodeLocalizedContentName($propName, $locale)
    {
        return sprintf(
            '%s:%s-%s',
            $this->namespaceRegistry->getAlias('localized-content'),
            $locale,
            $propName
        );
    }

    /**
     * Encode the given (non localized) property
     *
     * @param mixed $propName
     *
     * @return string
     */
    public function encodeContentName($propName)
    {
        return sprintf(
            '%s:%s',
            $this->namespaceRegistry->getAlias('content'),
            $propName
        );
    }
}
