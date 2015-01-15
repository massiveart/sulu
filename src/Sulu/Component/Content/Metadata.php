<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Content;

use JMS\Serializer\Annotation\Type;

/**
 * holds localized metadata
 */
class Metadata
{
    /**
     * @var array
     * @Type("array")
     */
    private $data;

    /**
     * @param array $metadata
     */
    public function __construct($metadata)
    {
        $this->data = $metadata;
    }

    /**
     * @param string $name meta data name
     * @param string $languageCode
     * @param string|null $default
     * @return string|null
     */
    public function get($name, $languageCode, $default = null)
    {
        if (isset($this->data[$name]) && isset($this->data[$name][$languageCode])) {
            return $this->data[$name][$languageCode];
        }

        return $default;
    }

    /**
     * return the meta from a specific language title not exists it will be null
     * @param $languageCode
     * @return array
     */
    public function getLanguageMeta($languageCode)
    {
        $meta = array();

        foreach ($this->data as $key => $languages) {
            $value = null;
            if (isset($languages[$languageCode])) {
                $value = $languages[$languageCode];
            }
            $meta[$key] = $value;
        }

        return $meta;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
