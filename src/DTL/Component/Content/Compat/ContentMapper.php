<?php

namespace DTL\Component\Content\Compat;

use Sulu\Component\Content\Mapper\ContentMapperInterface;
use PHPCR\Query\QueryInterface;
use PHPCR\Query\QueryResultInterface;
use Sulu\Component\Content\Mapper\ContentMapperRequest;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use DTL\Component\Content\Form\Exception\InvalidFormException;
use DTL\Component\Content\Compat\Structure\StructureManager;
use DTL\Component\Content\Document\DocumentInterface;
use Sulu\Component\Content\Types\ResourceLocator;
use Sulu\Component\PHPCR\SessionManager\SessionManagerInterface;

class ContentMapper implements ContentMapperInterface
{
    /**
     * @var DataNormalizer $dataNormalizer
     */
    private $dataNormalizer;

    /**
     * @var FormFactoryInterface $formFactory
     */
    private $formFactory;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var StructureManager
     */
    private $structureManager;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var ResourceLocator
     */
    private $resourceLocator;

    /**
     * @param DataNormalizer $dataNormalizer
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        DataNormalizer $dataNormalizer,
        FormFactoryInterface $formFactory,
        DocumentManager $documentManager,
        StructureManager $structureManager,
        SessionManagerInterface $sessionManager,
        ResourceLocator $resourceLocator
    )
    {
        $this->dataNormalizer = $dataNormalizer;
        $this->formFactory = $formFactory;
        $this->documentManager = $documentManager;
        $this->structureManager = $structureManager;
        $this->sessionManager = $sessionManager;
    }

    public function save(
        $data,
        $structureName,
        $webspaceKey,
        $locale,
        $userId,
        $partialUpdate = true,
        $uuid = null,
        $parent = null,
        $state = null,
        $isShadow = null,
        $shadowBaseLanguage = null,
        $structureType = Structure::TYPE_PAGE
    )
    {
        $data = $this->dataNormalizer->normalize($data);

        $document = null;

        if ($uuid) {
            $this->getDocument($uuid);
        }

        $form = $this->formFactory->create($structureType, $document, array(
            'webspace_key' => $webspaceKey,
            'locale' => $locale,
            'structure_name' => $structureName,
        ));
        $form->submit($data);

        if (!$form->isValid()) {
            throw new InvalidFormException($form);
        }

        $document = $form->getData();

        $this->documentManager->persist($document);
        $this->documentManager->flush();

        return $this->documentToStructure($document);
    }

    public function saveExtension(
        $uuid,
        $data,
        $extensionName,
        $webspaceKey,
        $languageCode,
        $userId
    )
    {
        throw new \InvalidArgumentException(sprintf(
            'Not implemented'
        ));
    }


    public function saveStartPage(
        $data,
        $structureName,
        $webspaceKey,
        $languageCode,
        $userId,
        $partialUpdate = true,
        $isShadow = null,
        $shadowBaseLanguage = null
    )
    {
        $uuid = $this->getContentNode($webspaceKey)->getIdentifier();
        $request = ContentMapperRequest::create('page')
            ->setTemplateKey($templateKey)
            ->setWebspaceKey($webspaceKey)
            ->setLocale($locale)
            ->setUuid($uuid)
            ->setUserId($userId)
            ->setPartialUpdate($partialUpdate)
            ->setIsShadow($isShadow)
            ->setShadowBaseLanguage($shadowBaseLanguage);

        return $this->saveRequest($request);
    }

    public function loadByParent(
        $uuid,
        $webspaceKey,
        $languageCode,
        $depth = 1,
        $flat = true,
        $ignoreExceptions = false,
        $excludeGhosts = false
    )
    {
        $parent = null;
        if ($uuid) {
            $parent = $this->getDocument($uuid);
        }

        if (null === $parent) {
            $parent = $this->getContentDocument($webspaceKey);
        }

        throw new \InvalidArgumentException('Implement me');
    }

    public function load($uuid, $webspaceKey, $locale, $loadGhostContent = false)
    {
    }

    public function loadByNode(
        NodeInterface $node,
        $locale,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false,
        $excludeShadow = true
    )
    {
        return $this->loadByDocument(
            $this->getDocument($uuid),
            $locale, $webspaceKey, $excludeGhost, $loadGhostContent, $excludeShadow
        );
    }

    public function loadStartPage($webspaceKey, $locale)
    {
        $document = $this->getContentDocument($webspaceKey, $locale);
        $structure = $this->documentToStructure($document);

        return $structure;
    }

    public function loadByResourceLocator($resourceLocator, $webspaceKey, $locale, $segmentKey = null)
    {
        $uuid = $this->resourceLocator->loadContentNodeUuid(
            $resourceLocator,
            $webspaceKey,
            $locale
        );

        $document = $this->getDocument($uuid);

        return $this->loadByDocument($document, $locale, $webspaceKey, true, false, false);
    }

    public function loadBySql2($sql2, $locale, $webspaceKey, $limit = null)
    {
        $query = $this->documentManager->createPhpcrQuery($sql2, QueryInterface::JCR_SQL2);

        if ($limit) {
            $query->setLimit($limit);
        }

        return $this->loadByQuery($query, $locale, $webspaceKey);
    }

    public function loadByQuery(QueryInterface $query, $languageCode, $webspaceKey, $excludeGhost = true, $loadGhostContent = false)
    {
        $documents = $this->documentManager->getDocumentsByPhpcrQuery($query);

        $structures = array();

        foreach ($documents as $document) {
            $structure = $this->documentToStructure($document);
            $structures[] = $structure;
        }

        return $structures;
    }

    public function loadTreeByUuid(
        $uuid,
        $locale,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false
    )
    {
        $document = $this->getDocument($uuid);

        list($result) = $this->loadTreeByDocument(
            $document, $locale, $webspaceKey, $excludeGhost, $loadGhostContent
        );

        return $result;
    }

    public function loadTreeByPath(
        $path,
        $locale,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false
    )
    {
        $path = ltrim($path, '/');

        if ($path === '') {
            $document = $this->getContentNode($webspaceKey);
        } else {
            $document = $this->getDocument(sprintf(
                '%s/%s',
                $this->sessionManager->getContentPath($webspaceKey),
                $path
            ));
        }

        list($result) = $this->loadTreeByDocument($document, $locale, $webspaceKey, $excludeGhost, $loadGhostContent);

        return $result;
    }

    public function loadBreadcrumb($uuid, $languageCode, $webspaceKey)
    {
        // switch to PHPCR-ODM QB?
        $sql = sprintf(
            "SELECT parent.[jcr:uuid], child.[jcr:uuid]
             FROM [nt:unstructured] AS child INNER JOIN [nt:unstructured] AS parent
                 ON ISDESCENDANTNODE(child, parent)
             WHERE child.[jcr:uuid]='%s'",
            $uuid
        );

        $query = $this->documentManager->createPhpcrQuery($sql, QueryInterface::JCR_SQL2);
        $nodes = $query->execute();

        $result = array();
        $groundDepth = $this->getContentDocument($webspaceKey)->getDepth();

        /** @var Row $row */
        foreach ($nodes->getRows() as $row) {
            $node = $row->getNode('parent');
            $nodeUuid = $node->getIdentifier();
            $depth = $node->getDepth() - $groundDepth;
            if ($depth >= 0) {
                $document = $this->documentManager->getUnitOfWork()->getOrCreateDocument(null, $node, array(
                    'locale' => $locale
                ));
                $result[$depth] = new BreadcrumbItem($depth, $nodeUuid, $document->getTitle());
            }
        }

        ksort($result);

        return $result;
    }

    public function delete($uuid, $webspaceKey)
    {
        $document = $this->getDocumentById($uuid);
        if ($document->getWebspaceKey() !== $webspaceKey) {
            throw new \InvalidArgumentException(sprintf(
                'Document "%s" does not belong to requested webspace "%s", cannot delete. It belong to "%s"',
                $document->getPath(),
                $webspaceKey,
                $document->getWebspaceKey()
            ));
        }

        $this->documentManager->deleteRecursively($document);
        $this->documentManager->flush();
    }

    public function move($uuid, $destParentUuid, $userId, $webspaceKey, $languageCode)
    {
        $document = $this->getDocumentById($uuid);
        $destParent = $this->getDocumentById($destParentUuid);

        $this->documentManager->move($document, $destParent->getPath());
    }

    /**
     * This should be pushed upstream to the DocumentManager
     */
    public function copy($uuid, $destParentUuid, $userId, $webspaceKey, $languageCode)
    {
        $parentDocument = $this->getDocumentById($destParentUuid);
        $document = $this->getDocumentById($uuid);
        $documentMetadata = $this->documentManager->getClassMetadata(ClassUtils::getClass($document));
        $refl = $documentMetadata->getReflectionClass();

        $locales = $this->documentManager->getLocalesFor($document);
        $copy = $refl->newInstance();
        $this->documentManager->persist($copy);

        foreach ($locales as $locale) {
            $translatedDocument = $this->documentManager->loadTranslation(null, $uuid, $locale);

            foreach ($refl->getProperties() as $name => $reflProperty) {
                if ($documentMetadata->isIdentifier($name)) {
                    continue;
                }

                $value = $reflProperty->getValue($translatedDocument);
                $reflProperty->setValue($copy, $value);
            }

            $this->documentManager->bindTranslation($copy, $locale);
        }

        $copy->setParent($parentDocument);
    }

    public function copyLanguage($uuid, $userId, $webspaceKey, $srcLanguageCode, $destLanguageCodes)
    {
        $document = $this->documentManager->loadTranslation(null, $uuid, $srcLanguageCode);

        foreach ($destLanguageCodes as $destLanguageCode) {
            $this->documentManager->bindTranslation($document, $destLanguageCode);
        }
    }

    public function orderBefore($uuid, $beforeUuid, $userId, $webspaceKey, $languageCode)
    {
        $document = $this->getDocumentById($uuid);
        $beforeDocument = $this->getDocumentType($beforeUuid);

        if (PathHelper::getParentPath($document->getPath()) !== PathHelper::getParentPath($beforeDocument)) {
            throw new \InvalidArgumentException(sprintf(
                'Both document and before document must be siblings, given "%s" and "%s"',
                $document->getPath(),
                $beforeDocument->getPath()
            ));
        }

        $this->documentManager->reorder(
            $document->getParent(),
            $document->getName(),
            $beforeDocument->getName(),
            true
        );
    }

    public function orderAt($uuid, $position, $userId, $webspaceKey, $languageCode)
    {
        $subject = $this->getDocumentById($uuid);
        $parent = $subject->getParent();

        $siblings = array_values($parent->getChildren()->toArray()); // get indexed array
        $countSiblings = count($siblings);
        $oldPosition = array_search($subject, $siblings) + 1;

        if ($countSiblings < $position || $position <= 0) {
            throw new InvalidOrderPositionException();
        }
        if ($position === $countSiblings) {
            $previousSibling = $siblings[$position - 1];
            $this->documentManager->reorder($parent, $subject->getName(), $previousSibling->getName());
            $this->documentManager->reorder($parent, $previousSibling->getName(), $subject->getName());
        } else if ($oldPosition < $position) {
            $this->documentManager->reorder($parent, $subject->getName(), $siblings[$position]->getName());
        } else if ($oldPosition > $position) {
            $this->documentManager->reorder($parent, $subject->getName(), $siblings[$position - 1]->getName());
        }

        return $this->documentToStructure($subject);
    }

    public function setNoRenamingFlag($noRenamingFlag)
    {
        throw new \InvalidArgumentException(
            'Not implemented'
        );
    }

    public function setIgnoreMandatoryFlag($ignoreMandatoryFlag)
    {
        throw new \InvalidArgumentException(
            'Not implemented'
        );
    }

    public function convertQueryResultToArray(
        QueryResultInterface $queryResult,
        $webspaceKey,
        $locales,
        $fields,
        $maxDepth
    )
    {
        $rootDepth = substr_count($this->sessionManager->getContentPath($webspaceKey), '/');

        $result = array();
        foreach ($locales as $locale) {
            /** @var \Jackalope\Query\Row $row */
            foreach ($queryResult->getRows() as $row) {
                $ids[] = $row->getPath();
            }
            $documents = $this->documentManager->findMany($ids);

            foreach ($documents as $document) {
                $document->setLocale($locale);
                $this->documentManager->refresh($document);

                $pageDepth = substr_count($row->getPath('page'), '/') - $rootDepth;

                if ($maxDepth === null || $maxDepth < 0 || ($maxDepth > 0 && $pageDepth <= $maxDepth)) {
                    $item = array(
                        'uuid' => $document->getUuid(),
                        'nodeType' => $document->getNodeType(),
                        'path' => $document->getShortPath(),
                        'changed' => $document->getChanged(),
                        'changer' => $document->getChanger(),
                        'created' => $document->getCreated(),
                        'published' => $document->getPublishedState(),
                        'creator' => $document->getCreator(),
                        'title' => $document->getResolvedTitle(), // TODO: needs to resolve the internal link title
                        'url' => $document->getCachedUrl(), // TODO: do this
                        'urls' => $document->getLocalizedUrls(),
                        'locale' => $locale,
                        'webspaceKey' => $document->getWebspaceKey(),
                        'template' => $document->getStructureType(),
                        'parent' => $document->getParent(),
                        'order' => $doucment->getOrder(),
                    );

                    if (false !== $item && !in_array($item, $result)) {
                        $result[] = $item;
                    }
                }
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'Fuck fuck fuck fuck fuck %s fuck fuck fuck fuck fuck',
            'Fuck'
        ));

        return $result;
    }

    public function saveRequest(ContentMapperRequest $request)
    {
        return $this->save(
            $request->getData(),
            $request->getTemplateKey(),
            $request->getWebspaceKey(),
            $request->getLocale(),
            $request->getUserId(),
            $request->getPartialUpdate(),
            $request->getUuid(),
            $request->getParentUuid(),
            $request->getState(),
            $request->getIsShadow(),
            $request->getShadowBaseLanguage(),
            $request->getType()
        );
    }

    public function restoreHistoryPath($path, $userId, $webspaceKey, $languageCode, $segmentKey = null)
    {
    }

    /**
     * Return a structure bridge corresponding to the given document
     *
     * @param DocumentInterface $document
     *
     * @return StructureBridge
     */
    private function documentToStructure(DocumentInterface $document)
    {
        $structureBridge = $this->structureManager->getStructure($document->getStructureType(), $document->getDocumentType());
        $structureBridge->setDocument($document);

        return $structureBridge;
    }

    /**
     * Return the document by path or UUID or throw an exception if it 
     * does not exist.
     *
     * @param mixed $uuid
     * @throws RuntimeEException If the document does not exist
     *
     * @return DocumentInterface
     */
    private function getDocument($id)
    {
        $document = $this->documentManager->find(null, $id);

        if (null === $document) {
            throw new \RuntimeException(sprintf(
                'Could not find document with ID "%s"',
                $uuid
            ));
        }

        return $document;
    }

    /**
     * Return the content document at the root of the webspaces
     * content tree.
     *
     * @param mixed $webspaceKey
     */
    private function getContentDocument($webspaceKey, $locale = null)
    {
        $contentPath = $this->sessionManager->getContentPath($webspaceKey);

        if ($locale) {
            $document = $this->documentManager->findTranslation(null, $contentPath, $locale);
        } else {
            $document = $this->documentManager->find(null, $contentPath);
        }

        if (null === $document) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot find content document at "%s"',
                $contentPath
            ));
        }

        return $document;
    }

    private function loadByDocument(
        DocumentInterface $document,
        $locale,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false,
        $excludeShadow = true
    ) 
    {
        $resolvedLocale = $locale;

        if (true === $loadGhostContent) {
            $resolvedLocale = $this->localizationFinder->getAvailableLocalization(
                $document->getPhpcrNode(),
                $locale,
                $webspaceKey
            );
        }

        if ($document->isShadowEnabled()) {
            $resolvedLocale = $document->getShadowLocale();
        }

        // TODO: Deprecate passing the webspace key
        if (!$webspaceKey) {
            $webspaceKey = $document->getWebspaceKey();
        }

        if (($excludeGhost && $excludeShadow) && $resolvedLocale != $locale) {
            return null;
        }

        $document->setLocale($resolvedLocale);
        $this->documentManager->refresh($document);

        return $this->documentToStructure($document);
    }

    private function loadTreeByDocument(
        DocumentInterface $document,
        $locale,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false,
        DocumentInterface $childDocument
    ) {
        // go up to content node
        if ($document->getDepth() > $this->getContentDocument($webspaceKey)->getDepth()) {
            list($globalResult, $nodeStructure) = $this->loadTreeByDocument(
                $node->getParent(),
                $locale,
                $webspaceKey,
                $excludeGhost,
                $loadGhostContent,
                $document
            );
        }

        // load children of node
        $result = array();
        $childStructure = null;
        foreach ($node as $child) {
            $structure = $this->loadByNode($child, $languageCode, $webspaceKey, $excludeGhost, $loadGhostContent);

            if ($structure === null) {
                continue;
            }

            $result[] = $structure;

            // search structure for child node
            if ($childNode !== null && $childNode === $child) {
                $childStructure = $structure;
            }
        }

        // set global result once
        if (!isset($globalResult)) {
            $globalResult = $result;
        }

        return array($globalResult, $childStructure);
    }
}
