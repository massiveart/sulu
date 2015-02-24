<?php

namespace DTL\Component\Content\Compat\Structure;

use Sulu\Component\Content\StructureManagerInterface;
use DTL\Component\Content\Structure\Structure;
use Sulu\Component\Content\StructureExtension\StructureExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DTL\Component\Content\Structure\Factory\StructureFactoryInterface;
use DTL\Component\Content\Compat\Structure\StructureBridge;

class StructureManager implements StructureManagerInterface
{
    /**
     * @var StructureFactoryInterface
     */
    private $structureFactory;

    /**
     * @param StructureFactoryInterface $structureFactory
     */
    public function __construct(StructureFactoryInterface $structureFactory)
    {
        $this->structureFactory = $structureFactory;
    }

    /**
     * Returns structure for given key and type
     * @param string $key
     * @param string $type
     * @return StructureInterface
     */
    public function getStructure($key, $type = 'page')
    {
        $structure = $this->structureFactory->getStructure($type, $key);
        $compatStructure = new StructureBridge($structure);

        return $compatStructure;
    }

    /**
     * Return all the structures of the given type
     * @param string $type
     * @return StructureInterface[]
     */
    public function getStructures($type = Structure::TYPE_PAGE)
    {
        $compatStructures = array();
        foreach ($this->structureFactory->getStructures() as $structure) {
            $compatStructures[] = new StructureBridge($structure);
        }

        return $compatStructures;
    }

    /**
     * add dynamically an extension to structures
     * @param StructureExtensionInterface $extension
     * @param string $template default is all templates
     */
    public function addExtension(StructureExtensionInterface $extension, $template = 'all')
    {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Returns extensions for structure
     * @param string $key
     * @return StructureExtensionInterface[]
     */
    public function getExtensions($key)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Indicates that the structure has a extension
     * @param string $key
     * @param string $name
     * @return boolean
     */
    public function hasExtension($key, $name)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Returns a extension
     * @param string $key
     * @param string $name
     * @return StructureExtensionInterface
     */
    public function getExtension($key, $name)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * This is not required.
     */
    public function setContainer(ContainerInterface $container = null)
    {
    }

}
