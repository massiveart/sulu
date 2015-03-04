<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\PhpcrOdm\Serializer;

use PHPCR\NodeInterface;
use DTL\Component\Content\Document\DocumentInterface;
use DTL\Component\Content\Structure\Factory\StructureFactory;
use DTL\Component\Content\PhpcrOdm\DocumentNodeHelper;

/**
 * Serialize content data into a series of properties in a single node.
 */
class FlatSerializer implements SerializerInterface
{
    const ARRAY_DELIM = '.';

    /**
     * @var StructureFactory
     */
    private $structureFactory;

    /**
     * @var DocumentNodeHelper
     */
    private $nodeHelper;

    /**
     * @param StructureFactory $structureFactory
     */
    public function __construct(
        StructureFactory $structureFactory, 
        DocumentNodeHelper $nodeHelper)
    {
        $this->structureFactory = $structureFactory;
        $this->nodeHelper = $nodeHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(DocumentInterface $document)
    {
        $node = $document->getPhpcrNode();
        $data = $document->getContent();
        $type = $document->getStructureType();

        $structure = $this->structureFactory->getStructure($document->getDocumentType(), $type);

        $localizedProps = array();
        $nonLocalizedProps = array();

        foreach ($data as $key => $value) {
            $isLocalized = $structure->getProperty($key)->localized;

            if ($isLocalized) {
                $localizedProps[$key] = $value;
                continue;
            }

            $nonLocalizedProps[$key] = $value;
        }

        foreach ($this->flatten($localizedProps) as $propName => $propValue) {
            $propName = $this->nodeHelper->encodeLocalizedContentName($propName, $document->getLocale());
            $node->setProperty($propName, $propValue);
        }

        foreach ($this->flatten($nonLocalizedProps) as $propName => $propValue) {
            $propName = $this->nodeHelper->encodeContentName($propName);
            $node->setProperty($propName, $propValue);
        }

        return $document;
    }

    /**
     * {@inheritDoc}
     */
    public function deserialize(DocumentInterface $document)
    {
        $node = $document->getPhpcrNode();
        $type = $document->getStructureType();

        $structure = $this->structureFactory->getStructure($document->getDocumentType(), $type);

        $nodeProperties = $node->getProperties();
        $flatData = array();

        foreach (array_keys($structure->getLocalizedProperties()) as $name) {
            $prefix = $this->nodeHelper->encodeLocalizedContentName($name, $document->getLocale());
            $flatData = array_merge($flatData, $this->extractProperties($name, $prefix, $nodeProperties));
        }

        foreach (array_keys($structure->getNonLocalizedProperties()) as $name) {
            $prefix = $this->nodeHelper->encodeContentName($name);
            $flatData = array_merge($flatData, $this->extractProperties($name, $prefix, $nodeProperties));
        }

        $result = array();
        foreach ($flatData as $key => $value) {
            $keys = explode(self::ARRAY_DELIM, $key);
            $result = array_merge_recursive(
                $result,
                $this->blowUp($keys, $value, $result)
            );
        }

        return $result;
    }

    /**
     * Extract the properties which match the given prefix, e.g.
     *
     *     i18n:de-animals.types
     *     cont:foo.bar.doo
     *     cont:title
     *
     * @param string $name
     * @param string $prefix
     * @param array $nodeProperties
     *
     * @return array Normalized key value array for properties which match the prefix
     */
    private function extractProperties($name, $prefix, &$nodeProperties)
    {
        $flatData = array();
        foreach ($nodeProperties as $propName => $prop) {
            if ($propName === $prefix) {
                $flatData[$name] = $prop->getValue();
                continue;
            }

            if (0 !== strpos($propName, $prefix . self::ARRAY_DELIM)) {
                continue;
            }

            $propName = substr($propName, strlen($prefix . self::ARRAY_DELIM));
            $flatData[$name . '.' . $propName] = $prop->getValue();
        }

        return $flatData;
    }

    /**
     * Convert the given multidimensional array into a flat array
     *
     * @param mixed $value
     * @param array $ancestors
     * @param array $result
     */
    private function flatten($value, $ancestors = array(), $result = array())
    {
        foreach ($value as $key => $value) {
            $currentAncestors = $ancestors;
            array_push($currentAncestors, $key);

            if (is_array($value)) {
                $result = $this->flatten($value, $currentAncestors, $result);
                continue;
            }

            $key = implode(self::ARRAY_DELIM, $currentAncestors);
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Hydate a multidimensional array using the given keys and value
     *
     * @param array $keys
     * @param mixed $value
     *
     * @return array
     */
    private function blowUp($keys, $value)
    {
        if (count($keys) == 0) {
            return $value;
        }

        $res = array();

        $key = array_shift($keys);
        $res[$key] = $this->blowUp($keys, $value);

        return $res;
    }
}
