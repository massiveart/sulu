<?php

/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
namespace DTL\Component\Content\PhpcrOdm;

/**
 * Central registry of roles to namespaces, e.g.
 */
class NamespaceRoleRegistry
{
    private $roleMap = array();

    /**
     * @param array $roleMap
     */
    public function __construct(array $roleMap)
    {
        $this->roleMap = $roleMap;
    }

    /**
     * Return the namespace alias for the given role, e.g. "localized_content" => "lcont"
     *
     * @return string
     */
    public function getAlias($role)
    {
        if (!isset($this->roleMap[$role])) {
            throw new \InvalidArgumentException(sprintf(
                'Trying to get non-existant namespace alias role "%s", known roles: "%s"',
                $role, implode('", "', array_keys($this->roleMap))
            ));
        }

        return $this->roleMap[$role];
    }
}
