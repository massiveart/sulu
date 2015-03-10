<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Document;

/**
 * Class which holds the workflow state constants and validation
 * method.
 *
 * Note that in the future this should be removed and workflows
 * should be dynamic.
 */
class WorkflowState
{
    /**
     * Document is published
     */
    const PUBLISHED = 'published';

    /**
     * Document is not published
     */
    const TEST = 'test';

    /**
     * Ensure that the given workflow state is valid
     *
     * @param string $state
     */
    public static function validateState($state)
    {
        $validStates = array(
            self::PUBLISHED,
            self::TEST,
        );

        if (!in_array($state, $validStates)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown workflow state "%s". Known states: "%s"',
                $state, implode('", "', $validStates)
            ));
        }
    }

    /**
     * Return true if the given state is published
     *
     * @param string $state
     *
     * @return boolean
     */
    public static function isPublished($state)
    {
        self::validateState($state);

        return $state === self::PUBLISHED;
    }
}
