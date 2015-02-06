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
use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;
use Symfony\Cmf\Component\RoutingAuto\UriContext;
use Doctrine\ODM\PHPCR\DocumentManager;
use Sulu\Component\PHPCR\SessionManager\SessionManager;
use Sulu\Component\Util\SuluNodeHelper;
use Sulu\Component\Webspace\Analyzer\RequestAnalyzerInterface;
use DTL\Bundle\ContentBundle\RoutingAuto\SuluPhpcrNodeAutoRoute;

class SuluPhpcrAdapter implements AdapterInterface
{
    const TAG_NO_MULTILANG = '_no_lang';

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var RequestAnalyzerInterface
     */
    private $requestAnalyzer;

    /**
     * @param DocumentManager $documentManager
     * @param SessionManager $sessionManager To retrieve the base path for the routes
     */
    public function __construct(
        DocumentManager $documentManager,
        SessionManager $sessionManager,
        RequestAnalyzerInterface $requestAnalyzer
    )
    {
        $this->documentManager = $documentManager;
        $this->sessionManager = $sessionManager;
        $this->requestAnalyzer = $requestAnalyzer;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocales($contentDocument)
    {
        return null;
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
        $session = $this->getPhpcrSession();
        $srcNode = $srcAutoRoute->getNode();
        $destNode = $destAutoRoute->getNode();
        $srcChildren = $srcNode->getNodes();

        foreach ($srcChildren as $srcChild) {
            $session->move($srcChild->getPath(), $destNode->getPath() . '/' . $srcChild->getName());
        }
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
        $path = rtrim($this->sessionManager->getRoutePath(
            $this->requestAnalyzer->getCurrentWebspace()->getKey(),
            $this->requestAnalyzer->getCurrentLocalization()
        ), '/');

        try {
            $session->getNode($path);
        } catch (PathNotFoundException $e) {
            throw new \RuntimeException(sprintf('The "route_basepath" configuration points to a non-existant path "%s".',
                $path
            ));
        }

        $uri = sprintf('%s/%s', $path, $uri);

        $node = NodeHelper::createPath($session, $uri);

        $contentNode = $session->getNodeByIdentifier($contentDocument->getUuid());

        $node->addMixin('sulu:path');
        $node->setProperty(SuluPhpcrNodeAutoRoute::PROPERTY_CONTENT, $contentNode);
        $node->setProperty(SuluPhpcrNodeAutoRoute::PROPERTY_HISTORY, false);
        $node->setProperty('sulu:created', new \DateTime());

        return new SuluPhpcrNodeAutoRoute($node);
    }

    /**
     * {@inheritDoc}
     */
    public function createRedirectRoute(AutoRouteInterface $referringAutoRoute, AutoRouteInterface $newRoute)
    {
        $referringRouteNode = $referringAutoRoute->getNode();
        $referringRouteNode->setProperty(SuluPhpcrNodeAutoRoute::PROPERTY_HISTORY, true);
        $referringRouteNode->setProperty(SuluPhpcrNodeAutoRoute::PROPERTY_CONTENT, $newRoute->getNode());
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

            $referrers[] = new SuluPhpcrNodeAutoRoute($reference);
        }

        return $referrers;
    }

    /**
     * {@inheritDoc}
     */
    public function findRouteForUri($uri)
    {
        $locale = $this->requestAnalyzer->getCurrentLocalization();
        $webspace = $this->requestAnalyzer->getCurrentWebspace()->getKey();
        $path = sprintf('%s%s',
            rtrim($this->sessionManager->getRoutePath($webspace, $locale), '/'),
            $uri
        );

        try {
            $node = $this->getPhpcrSession()->getNode($path);
        } catch (PathNotFoundException $e) {
            return null;
        }

        return new SuluPhpcrNodeAutoRoute($node);
    }

    private function getPhpcrSession()
    {
        return $this->documentManager->getPhpcrSession();
    }
}
