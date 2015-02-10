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
use Symfony\Cmf\Bundle\RoutingAutoBundle\Adapter\PhpcrOdmAdapter;
use DTL\Bundle\ContentBundle\Document\Route;
use Doctrine\ODM\PHPCR\Document\Generic;

class SuluPhpcrAdapter extends PhpcrOdmAdapter
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
        SessionManager $sessionManager
    )
    {
        parent::__construct($documentManager, null, 'DTL\Bundle\ContentBundle\Document\Route');
        $this->documentManager = $documentManager;
        $this->sessionManager = $sessionManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createAutoRoute($uri, $contentDocument, $autoRouteTag)
    {
        $path = rtrim($this->sessionManager->getRoutePath(
            $contentDocument->getWebspaceKey(),
            $contentDocument->getLocale()
        ), '/');

        try {
            $parentDocument = $this->documentManager->find(null, $path);
        } catch (PathNotFoundException $e) {
            throw new \RuntimeException(sprintf('The "route_basepath" configuration points to a non-existant path "%s".',
                $path
            ));
        }

        $segments = preg_split('#/#', $uri, null, PREG_SPLIT_NO_EMPTY);
        $headName = array_pop($segments);
        foreach ($segments as $segment) {
            $path .= '/' . $segment;
            $document = $this->dm->find(null, $path);

            if (null === $document) {
                $document = new Generic();
                $document->setParent($parentDocument);
                $document->setNodeName($segment);
                $this->dm->persist($document);
            }

            $parentDocument = $document;
        }

        $document = new Route();
        $document->setContent($contentDocument);
        $document->setName($headName);
        $document->setParent($parentDocument);
        $document->setType(AutoRouteInterface::TYPE_PRIMARY);
        $document->setCreated(new \DateTime());

        return $document;
    }

    /**
     * {@inheritDoc}
     */
    public function createRedirectRoute(AutoRouteInterface $referringAutoRoute, AutoRouteInterface $newRoute)
    {
        $referringAutoRoute->setType(AutoRouteInterface::TYPE_REDIRECT);
        $referringAutoRoute->setContent($newRoute);
    }

    /**
     * {@inheritDoc}
     */
    public function findRouteForUri($uri, UriContext $uriContext)
    {
        $subject = $uriContext->getSubjectObject();
        $webspace = $subject->getWebspaceKey();
        $locale = $subject->getLocale();
        $path = sprintf('%s%s',
            rtrim($this->sessionManager->getRoutePath($webspace, $locale), '/'),
            $uri
        );

        return $this->dm->find(null, $path);
    }
}
