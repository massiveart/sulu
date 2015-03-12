<?php

namespace DTL\Component\Content\Compat;

use Sulu\Component\Content\Mapper\ContentMapperInterface;
use PHPCR\Query\QueryInterface;
use PHPCR\NodeInterface;
use PHPCR\Query\QueryResultInterface;
use Sulu\Component\Content\Mapper\ContentMapperRequest;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use DTL\Component\Content\Form\Exception\InvalidFormException;
use DTL\Component\Content\Compat\Structure\StructureManager;
use DTL\Component\Content\Document\DocumentInterface;
use Sulu\Component\Content\Types\ResourceLocator;
use Sulu\Component\PHPCR\SessionManager\SessionManagerInterface;
use DTL\Component\Content\Document\LocalizationState;
use Sulu\Component\Content\Exception\ResourceLocatorNotFoundException;
use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;
use Sulu\Component\Content\BreadcrumbItem;

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
        $data = $this->dataNormalizer->normalize($data, $state, $parent);

        $document = null;

        if ($uuid) {
            $document = $this->getDocument($uuid, $locale);
        }

        $form = $this->formFactory->create($structureType, $document, array(
            'webspace_key' => $webspaceKey,
            'locale' => $locale,
            'structure_name' => $structureName,
        ));
        $form->submit($data, false);

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
        $locale,
        $userId,
        $partialUpdate = true,
        $isShadow = null,
        $shadowBaseLanguage = null
    )
    {
        $uuid = $this->getContentDocument($webspaceKey, $locale)->getUuid();
        $request = ContentMapperRequest::create('page')
            ->setTemplateKey($structureName)
            ->setType('homepage')
            ->setWebspaceKey($webspaceKey)
            ->setLocale($locale)
            ->setUuid($uuid)
            ->setUserId($userId)
            ->setPartialUpdate($partialUpdate)
            ->setIsShadow($isShadow)
            ->setData($data)
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
            $parent = $this->getDocument($uuid, $languageCode);
        }

        if (null === $parent) {
            $parent = $this->getContentDocument($webspaceKey, $languageCode);
        }

        $fetchDepth = -1;
        if (false === $flat) {
            $fetchDepth = $depth;
        }

        $children = $this->documentManager->getChildren($parent, null, $fetchDepth, $languageCode);
        $structures = $this->documentsToStructureCollection($children);

        if ($flat) {
            foreach ($children as $child) {
                if ($depth === null || $depth > 1) {
                    $childChildren = $this->loadByParent(
                        $child->getUuid(),
                        $webspaceKey,
                        $languageCode,
                        $depth - 1,
                        $flat,
                        $ignoreExceptions,
                        $excludeGhosts
                    );
                    $structures = array_merge($structures, $childChildren);
                }
            }
        }

        return $structures;
    }

    public function load($uuid, $webspaceKey, $locale, $loadGhostContent = false)
    {
        return $this->loadByDocument(
            $this->getDocument($uuid, $locale),
            $locale,
            array(
                'load_ghost_content' => $loadGhostContent,
                'exclude_ghost' => false,
                'exclude_shadow' => false,
            )
        );
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
            $this->getDocument($node->getIdentifier(), $locale),
            $locale,
            array(
                'load_ghost_content' => $loadGhostContent,
                'exclude_ghost' => $excludeGhost,
                'exclude_shadow' => $excludeShadow,
            )
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
        $path = $this->sessionManager->getRoutePath($webspaceKey, $locale);

        if ('/' !== $resourceLocator) {
            $path = sprintf('%s%s', $path, $resourceLocator);
        }

        $route = $this->getDocument($path, $locale);

        if (!$route instanceof AutoRouteInterface) {
            throw new ResourceLocatorNotFoundException(sprintf(
                'Expected to find route at path "%s" but got "%s"',
                $path, is_object($route) ? get_class($route) : gettype($route)
            ));
        }

        return $this->loadByDocument(
            $route->getContent(), 
            $locale,
            array(
                'exclude_shadow' => false,
            )
        );
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
            if ($excludeGhost && $document->isLocalizationState(LocalizationState::GHOST)) {
                continue;
            }

            $structure = $this->documentToStructure($document);
            $structures[] = $structure;
        }

        return $structures;
    }

    /**
     * TODO: I expect we will need to filter the children somehow
     * NOTE: UUID is not required
     */
    public function loadTreeByUuid(
        $uuid,
        $locale,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false
    )
    {
        $contentChildren = $this->getContentDocument($webspaceKey, $locale)->getChildren();

        return $this->loadCollectionByDocuments(
            $contentChildren,
            $locale,
            array(
                'load_ghost_content' => $loadGhostContent,
                'exclude_ghost' => $excludeGhost,
            )
        );
    }

    /**
     * NOTE: UUID and webspacekey are no longer required.
     */
    public function loadTreeByPath(
        $path,
        $locale,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false
    )
    {
        return $this->loadTreeByUuid(null, $locale, null, $excludeGhost, $loadGhostContent);
    }

    public function loadBreadcrumb($uuid, $locale, $webspaceKey)
    {
        $document = $this->getDocument($uuid, $locale);

        $documents = array();
        $contentDocument = $this->getContentDocument($webspaceKey, $locale);

        do {
            $documents[] = $document;
            $document = $document->getParent();
        } while ($document instanceof DocumentInterface && $document->getDepth() >= $contentDocument->getDepth());

        $items = array();
        foreach ($documents as $document) {
            $items[] = new BreadcrumbItem(
                $document->getDepth() - $contentDocument->getDepth(),
                $document->getUuid(),
                $document->getTitle()
            );
        }

        $items = array_reverse($items);

        return $items;
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
                $this->documentManager->loadTranslation(null, $document->getUuid(), $locale);

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

    private function documentsToStructureCollection($documents)
    {
        $collection = array();
        foreach ($documents as $document) {
            $collection[] = $this->documentToStructure($document);
        }

        return $collection;
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
    private function getDocument($id, $locale)
    {
        $document = $this->documentManager->findTranslation(null, $id, $locale);

        if (null === $document) {
            throw new \RuntimeException(sprintf(
                'Could not find document with ID "%s"',
                $id
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
    private function getContentDocument($webspaceKey, $locale)
    {
        $contentPath = $this->sessionManager->getContentPath($webspaceKey);
        $class = 'DTL\Component\Content\Document\DocumentInterface';

        if ($locale) {
            $document = $this->documentManager->findTranslation($class, $contentPath, $locale);
        } else {
            $document = $this->documentManager->find($class, $contentPath);
        }

        if (null === $document) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot find content document at "%s"',
                $contentPath
            ));
        }

        return $document;
    }

    private function loadCollectionByDocuments($documents, $locale, $options)
    {
        $collection = array();
        foreach ($documents as $document) {
            $collection[] = $this->loadByDocument($document, $locale, $options);
        }

        return $collection;
    }

    private function loadByDocument(DocumentInterface $document, $locale, $options)
    {
        $options = array_merge(array(
            'load_ghost_content' => false,
            'exclude_ghost' => true,
            'exclude_shadow' => true,
        ), $options);

        $isShadowOrGhost = $document->isLocalizationState(LocalizationState::GHOST) || $document->isLocalizationState(LocalizationState::SHADOW);
        if (($options['exclude_ghost'] && $options['exclude_shadow']) && $isShadowOrGhost) {
            return null;
        }

        $this->documentManager->findTranslation(null, $document->getUuid(), $locale);

        return $this->documentToStructure($document);
    }
}
