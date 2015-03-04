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
        $properties = $this->getLocalizedProperties($node, $name, $role);

        return array_keys($properties);
    }

    /**
     * Return the shadow locales which are enabled for the given
     * document node.
     */
    public function getEnabledShadowLocales(NodeInterface $node)
    {
        $properties = $this->getLocalizedProperties($node, 'shadowLocaleEnabled', 'localized-system');
        $properties = array_filter($properties, function ($property) {
            return $property->getValue();
        });

        return array_keys($properties);
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

    private function extractLocaleForName($namespaceAlias, $name, $propertyName)
    {
        if (!preg_match(sprintf('{%s:([a-z_A-Z]+)-%s}', $namespaceAlias, $name), $propertyName, $matches)) {
            return null;
        }

        return $matches[1];
    }

    private function getLocalizedProperties(NodeInterface $node, $name, $role)
    {
        $localizedProperties = array();
        $namespaceAlias = $this->namespaceRegistry->getAlias($role);

        $properties = $node->getProperties(sprintf(
            '%s:*', $namespaceAlias
        ));

        foreach ($properties as $property) {
            $locale = $this->extractLocaleForName($namespaceAlias, $name, $property->getName());

            if (null === $locale) {
                continue;
            }

            $localizedProperties[$locale] = $property;
        }

        return $localizedProperties;
    }
}
