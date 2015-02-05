<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 

namespace DTL\Bundle\ContentBundle\RoutingAuto;

use Doctrine\Bundle\PHPCRBundle\Initializer\InitializerInterface;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use Sulu\Component\Webspace\Manager\WebspaceManager;
use PHPCR\Util\NodeHelper;
use Symfony\Cmf\Component\RoutingAuto\AdapterInterface;
use PHPCR\PathNotFoundException;

class SuluPhpcrAdapter implements AdapterInterface
{
    private $documentManager;
    private $basePath;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager, $basePath)
    {
        $this->documentManager = $documentManager;
        $this->basePath = $basePath;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocales($contentDocument)
    {
        return $contnetDocument->getLocale();
    }

    /**
     * {@inheritDoc}
     */
    public function translateObject($contentDocument, $locale)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function generateAutoRouteTag(UriContext $uriContext)
    {
        return $uriContext->getLocale() ? : self::TAG_NO_MULTILANG;
    }

    /**
     * {@inheritDoc}
     */
    public function migrateAutoRouteChildren(AutoRouteInterface $srcAutoRoute, AutoRouteInterface $destAutoRoute)
    {
        // TODO
    }

    /**
     * {@inheritDoc}
     */
    public function removeAutoRoute(AutoRouteInterface $autoRoute)
    {
        $this->getPhpcrSession()->remove($autoRoute->getNode()->getPath());
    }

    /**
     * {@inheritDoc}
     */
    public function createAutoRoute($uri, $contentDocument, $autoRouteTag)
    {
        $session = $this->getPhpcrSession();
        $path = $this->baseRoutePath;

        try {
            $parentNode = $session->getNode($path);
        } catch (PathNotFoundException $e) {
            throw new \RuntimeException(sprintf('The "route_basepath" configuration points to a non-existant path "%s".',
                $path
            ));
        }

        $node = NodeHelper::createPath($session, $uri);

        $node->addMixin('sulu:path');
        $node->setProperty('sulu:content', $contentDocument->getUuid());
        $node->setProperty('sulu:history', false);
        $node->setProperty('sulu:created', new \DateTime());

        return new PhpcrNodeAutoRoute($node);
    }

    /**
     * {@inheritDoc}
     */
    public function createRedirectRoute(AutoRouteInterface $referringAutoRoute, AutoRouteInterface $newRoute)
    {
        $referringRouteNode = $referringAutoRoute->getNode();
        $referringRouteNode->setProperty('sulu:histroy', true);
        $referringRouteNode->setProperty('sulu:content', $newRoute->getNode());
    }

    /**
     * {@inheritDoc}
     */
    public function getRealClassName($className)
    {
        return $className;
    }

    /**
     * {@inheritDoc}
     */
    public function compareAutoRouteContent(AutoRouteInterface $autoRoute, $contentDocument)
    {
        return $autoRoute->getNode()->getIdentifier() == $contentDocument->getUuid();
    }

    /**
     * {@inheritDoc}
     */
    public function getReferringAutoRoutes($contentDocument)
    {
        $references = $contentDocument->getNode()->getReferences();
        $referrers = array();

        foreach ($references as $reference) {
            if (!$reference->isNodeType('sulu:path')) {
                continue;
            }

            $referrers[] = new PhpcrNodeAutoRoute($reference);
        }

        return $referrers;
    }

    /**
     * {@inheritDoc}
     */
    public function findRouteForUri($uri)
    {
        $path = $this->getPathFromUri($uri);

        try {
            $node = $this->getPhpcrSession()->getNode($path);
        } catch (PathNotFoundException $e) {
            return null;
        }

        return new PhpcrNodeAutoRoute($node);
    }

    private function getPhpcrSession()
    {
        return $this->documentManager->getPhpcrSession();
    }

    private function getPathFromUri($uri)
    {
        return $this->basePath . $uri;
    }
}
