<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\PhpcrOdm\Serializer;

class PropertyNameEncoder implements PropertyNameEncoderInterface
{
    private $localizedNamespace;
    private $contentNamespace;

    public function __construct($localizedNamespace, $contentNamespace)
    {
        $this->localizedNamespace = $localizedNamespace;
        $this->contentNamespace = $contentNamespace;
    }

    public function encodeLocalized($propName, $locale)
    {
        return $this->localizedNamespace . ':' . $locale . '-' . $propName;
    }

    public function encode($propName)
    {
        return $this->contentNamespace . ':' . $propName;
    }
}
