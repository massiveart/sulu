<?php

/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
namespace DTL\Component\Content\Structure;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\FileLocator;
use Doctrine\Common\Inflector\Inflector;

/**
 * Create new (mapped) structures using the provided loader.
 */
class StructureFactory
{
    /**
     * @var array
     */
    private $typePaths = array();

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @param LoaderInterface $loader
     * @param array $typePaths
     * @param mixed $cachePath
     * @param mixed $debug
     */
    public function __construct(LoaderInterface $loader, array $typePaths, $cachePath, $debug = false)
    {
        $this->typePaths = $typePaths;
        $this->cachePath = $cachePath;
        $this->loader = $loader;
        $this->debug = $debug;
    }

    /**
     * Return the structure of the given $type and $structureType
     *
     * @param mixed $type The primary system type, e.g. page, snippet
     * @param mixed $structureType The secondary user type
     */
    public function getStructure($type, $structureType)
    {
        if (!isset($this->typePaths[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Structure type "%s" is not mapped. Mapped structure types: "%s"',
                $structureType,
                implode('", "', array_keys($this->typePaths))
            ));
        }

        $cachePath = sprintf(
            '%s/%s%s', 
            $this->cachePath,
            Inflector::camelize($type),
            Inflector::camelize($structureType)
        );

        $cache = new ConfigCache($cachePath, $this->debug);

        if (!$cache->isFresh()) {
            $fileLocator = new FileLocator($this->typePaths[$type]);
            $filePath = $fileLocator->locate(sprintf('%s.xml', $structureType));

            $metadata =  $this->loader->load($filePath);
            $resources = array(new FileResource($filePath));

            $cache->write(
                sprintf('<?php $metadata = \'%s\';', serialize($metadata)),
                $resources
            );
        }

        require($cachePath);

        return unserialize($metadata);
    }
}
