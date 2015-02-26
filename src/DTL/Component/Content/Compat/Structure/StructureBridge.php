<?php

namespace DTL\Component\Content\Compat\Structure;

use Sulu\Component\Content\StructureInterface;
use DTL\Component\Content\Structure\Structure;
use DTL\Bundle\ContentBundle\Document\Document;
use Sulu\Component\Content\Property;
use DTL\Component\Content\Structure\Property as NewProperty;
use DTL\Component\Content\Document\PageInterface;
use Sulu\Component\Content\PropertyTag;
use Sulu\Component\Content\Section\SectionProperty;
use Sulu\Component\Content\Block\BlockProperty;
use Sulu\Component\Content\Block\BlockPropertyType;

class StructureBridge implements StructureInterface
{
    /**
     * @var Structure
     */
    private $structure;

    /**
     * @var Document
     */
    private $document;

    /**
     * @param Structure $structure
     */
    public function __construct(Structure $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @param DocumentInterface $document
     */
    public function setDocument(DocumentInterface $document)
    {
        $this->document = $document;
    }

    /**
     * {@inheritDoc}
     */
    public function setLanguageCode($language)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getLanguageCode()
    {
        return $this->getDocument()->getLocale();
    }

    /**
     * {@inheritDoc}
     */
    public function setWebspaceKey($webspace)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getWebspaceKey()
    {
        return $this->getDocument()->getWebspaceKey();
    }

    /**
     * {@inheritDoc}
     */
    public function getUuid()
    {
        return $this->getDocument()->getUuid();
    }

    /**
     * {@inheritDoc}
     */
    public function setUuid($uuid)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreator()
    {
        return $this->getDocument()->getCreator();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreator($userId)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getChanger()
    {
        return $this->getDocument()->getChanger();
    }

    /**
     * {@inheritDoc}
     */
    public function setChanger($userId)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreated()
    {
        return $this->getDocument()->getCreated();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreated(DateTime $created)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getChanged()
    {
        return $this->getDocument()->getChanged();
    }

    /**
     * {@inheritDoc}
     */
    public function setChanged(DateTime $changed)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return $this->structure->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperty($name)
    {
        $property = $this->structure->getProperty($name);

        $propertyBridge = $this->createBridgeFromProperty($name, $property);

        return $propertyBridge;
    }

    /**
     * {@inheritDoc}
     */
    public function hasProperty($name)
    {
        return $this->structure->hasProperty($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties($flatten = false)
    {
        $propertyBridges = array();
        foreach (array_keys($this->structure->properties) as $propertyName) {
            $propertyBridges[$propertyName] = $this->getProperty($propertyName);
        }

        return $propertyBridges;
    }

    /**
     * {@inheritDoc}
     */
    public function setHasChildren($hasChildren)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getHasChildren()
    {
        return $this->getDocument()->hasChildren();
    }

    /**
     * {@inheritDoc}
     */
    public function setChildren($children)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        $children = array();

        foreach ($this->getDocument()->getChildren() as $child) {
            $children[] = new $this($this->structure, $child);
        }

        return $children;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublishedState()
    {
        return $this->getPage()->getPublishedState();
    }

    /**
     * {@inheritDoc}
     */
    public function setPublished($published)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getPublished()
    {
        return $this->getPage()->getPublished();
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyValue($name)
    {
        $this->notImplemented(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyNames()
    {
        return array_keys($this->structure->properties);
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        // should return either page or snippet ..
        $this->notImplemented(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->getDocument()->getPath();
    }

    /**
     * {@inheritDoc}
     */
    public function setPath($path)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function setHasTranslation($hasTranslation)
    {
        $this->readOnlyException(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getHasTranslation()
    {
        return $this->structure->getLocalizedProperties() ? true : false;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray($complete = true)
    {
        $this->notImplemented(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyByTagName($tagName, $highest = true)
    {
        $this->notImplemented(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertiesByTagName($tagName)
    {
        $this->notImplemented(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyValueByTagName($tagName)
    {
        $this->notImplemented(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function hasTag($tag)
    {
        $this->notImplemented(__METHOD__);
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeType()
    {
        $this->notImplemented();
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeName()
    {
        return $this->getDocument()->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getLocalizedTitle($languageCode)
    {
        return $this->structure->getLocalizedTitle($languageCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeState()
    {
        return $this->structure->getWorkflowStage();
    }

    /**
     * {@inheritDoc}
     */
    public function copyFrom(StructureInterface $structure)
    {
        $this->notImplemented(__METHOD__);
    }

    private function getSectionProperty($name, NewProperty $property)
    {
        $sectionProperty = new SectionProperty(
            $name,
            array(
                'title' => $property->title,
                'infoText' => $property->description,
            ),
            $property->colSpan
        );

        foreach ($property->children as $childName => $child) {
            $sectionProperty->addChild($this->createBridgeFromProperty($childName, $child));
        }

        return $sectionProperty;
    }

    private function getBlockProperty($name, NewProperty $property)
    {
        $blockProperty = new BlockProperty(
            $name,
            array(
                'title' => $property->title,
                'infoText' => $property->description,
            ),
            $property->type,
            $property->required,
            $property->localized,
            $property->maxOccurs,
            $property->minOccurs,
            $property->parameters,
            array(),
            $property->colSpan
        );

        foreach ($property->parameters['prototypes'] as $prototypeName => $prototype) {
            $blockType = new BlockPropertyType(
                $prototypeName,
                array(
                    'title' => $prototype->title,
                )
            );
        }

        return $blockProperty;
    }

    private function createBridgeFromProperty($name, NewProperty $property)
    {
        if ($property->type === 'section') {
            return $this->getSectionProperty($name, $property);
        }

        if ($property->type === 'block') {
            return $this->getBlockProperty($name, $property);
        }

        $propertyBridge = new Property(
            $name,
            array(
                'title' => $property->title,
                'infoText' => $property->description,
            ),
            $property->type,
            $property->required,
            $property->localized,
            $property->maxOccurs,
            $property->minOccurs,
            $property->parameters,
            array(),
            $property->colSpan
        );

        foreach ($property->tags as $tag) {
            $propertyBridge->addTag(new PropertyTag($tag->name, $tag->priority, $tag->attributes));
        }

        return $propertyBridge;
    }

    private function getDocument()
    {
        if (!$this->document) {
            throw new \RuntimeException(
                'Document has not been applied to structure yet, cannot retrieve data from structure.'
            );
        }

        return $this->document;
    }

    private function readOnlyException($method)
    {
        throw new \BadMethodCallException(sprintf(
            'Compatibility layer StructureBridge instances are readonly. Tried to call "%s"',
            $method
        ));
    }

    private function getPage()
    {
        $document = $this->getDocument();
        if (!$document instanceof PageInterface) {
            throw new \BadMethodCallException(sprintf(
                'Cannot call getPublishedState on Document which does not implement PageInterface. Is "%s"',
                get_class($document)
            ));
        }

        return $document;
    }

    private function notImplemented($method)
    {
        throw new \InvalidArgumentException(sprintf(
            'Method "%s" is not yet implemented', $method
        ));
    }
}
