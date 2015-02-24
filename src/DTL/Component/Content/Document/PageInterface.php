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
     * Return the stage of the workflow
     *
     * Return value should represent workflow stages, e.g.
     * testing, review, published, etc.
     *
     * @return mixed
     */
    public function getWorkflowStage();
}
