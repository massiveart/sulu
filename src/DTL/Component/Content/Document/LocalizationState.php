<?php

namespace DTL\Component\Content\Document;

class LocalizationState
{
    /**
     * The document is loaded in the requested locale,
     */
    const LOCALIZED = 'localized';

    /**
     * The document was not requested in any specific locale, locale
     * was automatically selected.
     */
    const AUTO = 'auto';

    /**
     * The document is not available in the requested locale
     * and a fallback has been used instead.
     */
    const GHOST = 'ghost';

    /**
     * The document specified its own fallback for the requested
     * locale.
     *
     * This status does not apply to non-primary document types such
     * as snippets.
     */
    const SHADOW = 'shadow';

    /**
     * {@inheritDoc}
     */
    static public function getValidLocalizationStates()
    {
        return array(
            self::LOCALIZED,
            self::GHOST,
            self::SHADOW,
        );
    }
}
