<?php

namespace DTL\Component\Content\Compat;

/**
 * Normalizes the legacy Sulu request data
 */
class DataNormalizer
{
    public function normalize($data)
    {
        $normalized = array(
            'title' => $this->getAndUnsetValue($data, 'title'),
            'resourceLocator' => $this->getAndUnsetValue($data, 'url'),
            'redirectType' => $this->getAndUnsetValue($data, 'nodeType'),
            'navigationContexts' => $this->getAndUnsetValue($data, 'navContexts'),
            'content' => $data
        );

        return $normalized;
    }

    private function getAndUnsetValue(&$data, $key)
    {
        $value = null;

        if (isset($data[$key])) {
            $value = $data[$key];
            unset($data[$key]);
        }

        return $value;
    }
}
