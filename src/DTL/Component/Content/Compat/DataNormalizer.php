<?php

namespace DTL\Component\Content\Compat;

use Sulu\Component\Content\StructureInterface;
use DTL\Component\Content\Document\WorkflowState;
use Sulu\Component\Content\Structure as LegacyStructure;

/**
 * Normalizes the legacy Sulu request data
 */
class DataNormalizer
{
    /**
     * Normalize incoming data from the legacy node controller
     *
     * @param mixed $data
     * @param mixed $state Translates to the workflow state
     */
    public function normalize($data, $state)
    {
        unset(
            $data['template'],
            $data['originTemplate'],
            $data['changed'],
            $data['changer'],
            $data['path'],
            $data['nodeState'],
            $data['internal'],
            $data['concreteLanguages'],
            $data['hasSub'],
            $data['published'],
            $data['enabledShadowLanguages'],
            $data['shadowOn'],
            $data['shadowBaseLanguage'],
            $data['created'],
            $data['_embedded'],
            $data['_links'],
            $data['navigation'],
            $data['id']
        );

        $normalized = array(
            'title' => $this->getAndUnsetValue($data, 'title'),
            'resourceLocator' => $this->getAndUnsetValue($data, 'url'),
            'redirectType' => $this->getAndUnsetRedirectType($data),
            'navigationContexts' => $this->getAndUnsetValue($data, 'navContexts'),
            'workflowState' => $this->getWorkflowState($state),
            'content' => $data,
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

    private function getWorkflowState($state)
    {
        if ($state === StructureInterface::STATE_PUBLISHED) {
            return WorkflowState::PUBLISHED;
        }

        return WorkflowState::TEST;
    }

    private function getAndUnsetRedirectType(&$data)
    {
        if (!isset($data['nodeType'])) {
            return null;
        }

        $nodeType = $data['nodeType'];
        unset($data['nodeType']);

        switch ($nodeType) {
            case LegacyStructure::NODE_TYPE_INTERNAL_LINK:
                return PageInterface::REDIRECT_TYPE_INTERNAL;
            case LegacyStructure::NODE_TYPE_EXTERNAL_LINK:
                return PageInterface::REDIRECT_TYPE_EXTERNAL;
            default:
                return null;
        }
    }
}
