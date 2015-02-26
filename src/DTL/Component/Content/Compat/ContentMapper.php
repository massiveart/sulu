<?php

namespace DTL\Component\Content\Compat;

use Sulu\Component\Content\Mapper\ContentMapperInterface;
use PHPCR\Query\QueryInterface;
use PHPCR\Query\QueryResultInterface;
use Sulu\Component\Content\Mapper\ContentMapperRequest;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use DTL\Component\Content\Form\Exception\InvalidFormException;

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
     * @param DataNormalizer $dataNormalizer
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        DataNormalizer $dataNormalizer,
        FormFactoryInterface $formFactory,
        DocumentManager $documentManager
    )
    {
        $this->dataNormalizer = $dataNormalizer;
        $this->formFactory = $formFactory;
        $this->documentManager = $documentManager;
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
            $document = $this->documentManager->find(null, $uuid);

            if (null === $document) {
                throw new \InvalidArgumentException(sprintf(
                    'Could not find document with ID "%s"',
                    $uuid
                ));
            }
        }

        $form = $this->formFactory->create($structureType, $document, array(
            'webspace_key' => $webspaceKey,
            'locale' => $locale,
            'structure_name' => $structureName,
        ));
        $form->submit($data);

        if ($form->isValid()) {
            $document = $form->getData();

            $this->documentManager->persist($document);
            $this->documentManager->flush();
            return;
        }

        throw new InvalidFormException($form);
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
    }

    public function load($uuid, $webspaceKey, $languageCode, $loadGhostContent = false)
    {
    }

    public function loadStartPage($webspaceKey, $languageCode)
    {
    }

    public function loadByResourceLocator($resourceLocator, $webspaceKey, $languageCode, $segmentKey = null)
    {
    }

    public function loadBySql2($sql2, $languageCode, $webspaceKey, $limit = null)
    {
    }

    public function loadByQuery(QueryInterface $query, $languageCode, $webspaceKey, $excludeGhost = true, $loadGhostContent = false)
    {
    }

    public function loadTreeByUuid(
        $uuid,
        $languageCode,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false
    )
    {
    }

    public function loadTreeByPath(
        $path,
        $languageCode,
        $webspaceKey,
        $excludeGhost = true,
        $loadGhostContent = false
    )
    {
    }

    public function loadBreadcrumb($uuid, $languageCode, $webspaceKey)
    {
    }

    public function delete($uuid, $webspaceKey)
    {
    }

    public function move($uuid, $destParentUuid, $userId, $webspaceKey, $languageCode)
    {
    }

    public function copy($uuid, $destParentUuid, $userId, $webspaceKey, $languageCode)
    {
    }

    public function copyLanguage($uuid, $userId, $webspaceKey, $srcLanguageCode, $destLanguageCodes)
    {
    }

    public function orderBefore($uuid, $beforeUuid, $userId, $webspaceKey, $languageCode)
    {
    }

    public function orderAt($uuid, $position, $userId, $webspaceKey, $languageCode)
    {
    }

    public function setNoRenamingFlag($noRenamingFlag)
    {
    }

    public function setIgnoreMandatoryFlag($ignoreMandatoryFlag)
    {
    }

    public function convertQueryResultToArray(
        QueryResultInterface $queryResult,
        $webspaceKey,
        $locales,
        $fields,
        $maxDepth
    )
    {
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
}
