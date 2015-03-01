<?php

namespace DTL\Bundle\ContentBundle\Initializer;

use Doctrine\Bundle\PHPCRBundle\Initializer\InitializerInterface;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use Sulu\Component\Webspace\Manager\WebspaceManager;
use PHPCR\Util\NodeHelper;
use DTL\Bundle\ContentBundle\Document\Route;
use Sulu\Component\Content\StructureInterface;
use DTL\Component\Content\Document\PageInterface;
use Sulu\Component\Webspace\Webspace;
use Doctrine\ODM\PHPCR\DocumentManager;
use DTL\Bundle\ContentBundle\Document\PageDocument;

class PhpcrOdmInitializer implements InitializerInterface
{
    private $paths;
    private $webspaceManager;

    public function __construct(WebspaceManager $webspaceManager, $paths = array())
    {
        $this->webspaceManager = $webspaceManager;
        $this->paths = array_merge(array(
            'base' => '/cmf',
            'content' => 'contents',
            'route' => 'routes',
            'snippet' => 'snippets',
        ), $paths);
    }

    public function getName()
    {
        return 'Sulu initializer';
    }

    public function init(ManagerRegistry $registry)
    {
        $documentManager = $registry->getManager();
        $session = $documentManager->getPhpcrSession();
        $baseNode = NodeHelper::createPath($session, $this->paths['base']);

        $webspaceCollection = $this->webspaceManager->getWebspaceCollection();

        foreach ($webspaceCollection as $webspace) {
            $webspaceKey = $webspace->getKey();

            if ($baseNode->hasNode($webspaceKey)) {
                $webspaceNode = $baseNode->getNode($webspaceKey);
            } else {
                $webspaceNode = $baseNode->addNode($webspaceKey, 'sulu:webspace');
            }

            if (!$webspaceNode->hasNode('routes')) {
                $webspaceNode->addNode('routes');
            }

            $session->save();
            $webspaceDocument = $documentManager->find(null, $webspaceNode->getPath());

            $homepage = $this->createHomepage($documentManager, $webspaceDocument, $webspace);

            $this->createRoutes($documentManager, $webspace, $homepage);
        }

        $documentManager->flush();
    }

    private function createHomepage(DocumentManager $manager, $webspaceDocument)
    {
        $page = new PageDocument();
        $page->setStructureType('overview');
        $page->setResourceLocator('');
        $page->setLocale('en');
        $page->setWorkflowStage(StructureInterface::STATE_PUBLISHED);
        $page->setParent($webspaceDocument);
        $manager->persist($page);

        return $page;
    }

    public function createRoutes(DocumentManager $homepage, Webspace $webspace, PageInterface $page)
    {
    }
}
