<?php

namespace DTL\Component\Content\Serializer;

interface PropertyNameEncoderInterface
{
    public function encodeLocalized($propName, $locale);

    public function encode($propName);
}
