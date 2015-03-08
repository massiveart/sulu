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

use Doctrine\ODM\PHPCR\ChildrenCollection;

/**
 * Documents implementing this interface represent content
 * units which can be accessed at a URL on the web.
 */
interface PageInterface extends DocumentInterface
{
    const REDIRECT_TYPE_INTERNAL = 'internal';
    const REDIRECT_TYPE_EXTERNAL = 'external';

    /**
     * Return the type of redirection to perform when the
     * page is loaded. Returning null indicates no redirection.
     *
     * Must return one of the PageInterface::REDIRECT_TYPE_* constants.
     *
     * @return string
     */
    public function getRedirectType();

    /**
     *
    public function isRedirectType($type);

    /**
     * Return the resource locator (i.e. the URI)
     * for this document
     *
     * @return string
     */
    public function getResourceLocator();

    /**
     * Set the resource locator
     *
     * @param string
     */
    public function setResourceLocator($resourceLocator);

    /**
     * Return the published state of this document
     *
     * @return integer
     */
    public function getPublishedState();

    /**
     * Return the date upon which the Page was published
     *
     * @return DateTime
     */
    public function getPublished();

    /**
     * Return a list of all enabled shadow locales on this node which are
     * shadows of another locale.  Therefore, the return value of this method
     * is independent of the current locale.
     *
     * @return string[]
     */
    public function getShadowLocales();

    /**
     * Return true if shadow localization has been enabled on this page
     *
     * @return boolean
     */
    public function getShadowLocaleEnabled();

    /**
     * Enable or disable shadow localization on this page
     *
     * @param boolean $shadowLocaleEnabled
     */
    public function setShadowLocaleEnabled($shadowLocaleEnabled);

    /**
     * Return all real locales whch exist for this page (excluding shadow locales)
     *
     * @return string[]
     */
    public function getRealLocales();

    /**
     * Return the shadow locale for this page for it's current locale (as
     * determined by getLocale). The value may be null if the shadow locale has
     * not yet been used.
     *
     * @return string|null
     */
    public function getShadowLocale();
}
