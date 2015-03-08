<?php

namespace DTL\Component\Content\PhpcrOdm\LocaleChooser;

use Doctrine\ODM\PHPCR\Translation\LocaleChooser\LocaleChooserInterface;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Analyzer\RequestAnalyzerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use DTL\Component\Content\Document\DocumentInterface;
use DTL\Component\Content\PhpcrOdm\DocumentNodeHelper;

class SuluLocaleChooser implements LocaleChooserInterface
{
    /**
     * @var RequestAnalyzerInterface
     */
    private $requestAnalyzer;

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    /**
     * @var DocumentNodeHelper
     */
    private $documentNodeHelper;

    public function __construct(
        RequestAnalyzerInterface $requestAnalyzer,
        WebspaceManagerInterface $webspaceManager,
        DocumentNodeHelper $documentNodeHelper
    )
    {
        $this->webspaceManager = $webspaceManager;
        $this->requestAnalyzer = $requestAnalyzer;
        $this->documentNodeHelper = $documentNodeHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getFallbackLocales($document, ClassMetadata $metadata, $forLocale = null)
    {
        if (!$document instanceof DocumentInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Document must be instance of DocumentInterface, got "%s"',
                get_class($document)
            ));
        }

        if (null === $document->getPhpcrNode()) {
            $document->setRequestedLocale($forLocale);
            return array();
        }

        $this->setRequestedLocale($document, $forLocale);

        return $this->doGetFallbackLocales($document, $metadata, $forLocale);
    }

    /**
     * {@inheritDoc}
     */
    public function getLocale()
    {
        return $this->requestAnalyzer->getCurrentLocalization();
    }

    private function setRequestedLocale($document, $forLocale)
    {
        $locales = $this->documentNodeHelper->getLocales($document->getPhpcrNode());

        if (false === in_array($forLocale, $locales)) {
            $document->setRequestedLocale($forLocale);
        }
    }

    private function doGetFallbackLocales($document, ClassMetadata $metadata, $forLocale = null)
    {
        $webspace = $this->requestAnalyzer->getWebspace();

        if (null === $webspace) {
            return $this->getOtherLocales($document, $forLocale);
        }

        $localization = $webspace->getLocalization($forLocale);

        $locales = array_merge(
            $this->getParentLocalizations($localization),
            $this->getChildLocalizations($localization),
            $this->documentNodeHelper->getLocales($document->getPhpcrNode())
        );

        $locales = array_filter($locales, function ($locale) use ($forLocale) {
            return $locale !== $forLocale;
        });

        $locales = array_unique($locales);

        return array_values($locales);
    }

    private function getOtherLocales(DocumentInterface $document, $forLocale)
    {
        $locales = $this->documentNodeHelper->getLocales($document->getPhpcrNode());
        return array_filter($locales, function ($locale) use ($forLocale) {
            return $locale !== $forLocale;
        });

    }

    private function getParentLocalizations(Localization $localization)
    {
        $locales = array();

        do {
            $locales[] = $localization->getLocalization('_');
            $localization = $localization->getParent();
        } while ($localization != null);

        return $locales;
    }

    private function getChildLocalizations(Localization $localization, $locales = array())
    {
        $childLocalizations = $localization->getChildren();

        if (empty($childLocalizations)) {
            return $locales;
        }

        foreach ($childLocalizations as $childLocalization) {
            $locales[] = $childLocalization->getLocalization('_');
            $locales = $this->getChildLocalizations($childLocalization, $locales);
        }

        return $locales;
    }

    // The following methods  are required by the interface but are infact not required.
    // See: https://github.com/doctrine/phpcr-odm/issues/604
    public function setLocale($locale) {}
    public function getDefaultLocalesOrder() {}
    public function getDefaultLocale() {}
    public function setLocalePreference($localPreference) {}
    public function setFallbackLocales($locale, array $order, $replace = false) {}
}
