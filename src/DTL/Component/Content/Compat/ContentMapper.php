<?php

namespace DTL\Component\Content\Compat;

use Sulu\Component\Content\Mapper\ContentMapperInterface;

class ContentMapper implements ContentMapperInterface
{
    public function save(
        $data,
        $templateKey,
        $webspaceKey,
        $languageCode,
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
        $data = $this->dataNormalizer->normalizeData($data);
        $form = $this->formFactory->create($structureType);

        $document = null;

        if ($uuid) {
            $document = $this->documentManager->find(null, $uuid);

            if (null === $document) {
                throw new \InvalidArgumentException(sprintf(
                    'Could not find document with ID "%s"',
                    $uuid
                ));
            }

            $form->setData($document);
        }

        $form->submit($data);

        if ($form->isValid()) {
            $this->documentManager->persist($document);
            $this->documentManager->flush();
        }
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
        $templateKey,
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
    }

    public function restoreHistoryPath($path, $userId, $webspaceKey, $languageCode, $segmentKey = null)
    {
    }
}
