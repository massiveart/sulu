<?php

namespace DTL\Component\Content\Serializer;

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
