<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\SearchBundle\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Navigation\Navigation;
use Sulu\Bundle\AdminBundle\Navigation\NavigationItem;
use Sulu\Bundle\SecurityBundle\Permission\SecurityCheckerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Component\Webspace\Webspace;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SuluSearchAdmin extends Admin
{

    public function __construct($title) {
        $rootNavigationItem = new NavigationItem($title);

        $section = new NavigationItem('');

        $rootNavigationItem->addChild($section);

        $search = new NavigationItem('navigation.search');
        $search->setIcon('search');
        $search->setAction('search');

        $section->addChild($search);

        $this->setNavigation(new Navigation($rootNavigationItem));
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getJsBundleName()
    {
        return 'sulusearch';
    }

    /**
     * {@inheritDoc}
     */
    public function getSecurityContexts()
    {
        return array();
    }
}