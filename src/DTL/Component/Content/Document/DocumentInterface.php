<?php
/*
 * This file is part of the Sulu CMS.

 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Document;

use Doctrine\ODM\PHPCR\ChildrenCollection;
use DTL\Component\Content\PhpcrOdm\NamespaceRoleRegistry;
use DTL\Component\Content\PhpcrOdm\DocumentNodeHelper;

/**
 * Documents are are the base content units in Sulu all
 * documents need to implement this interface.
 */
interface DocumentInterface
{
    /**
     * Return the (node) name of this document
     *
     * @return string
     */
    public function getName();

    /**
     * Return the title of this document
     *
     * @return string
     */
    public function getTitle();

    /**
     * Return the PHPCR UUID of this document
     *
     * @return string
     */
    public function getUuid();

    /**
     * Return the path to this document in the content repository
     *
     * @return string
     */
    public function getPath();

    /**
     * Set the (mapped) parent object of this document
     *
     * @param object
     */
    public function setParent($parent);

    /**
     * Return the parent of this document
     *
     * @return object
     */
    public function getParent();

    /**
     * Return the children of this document
     *
     * @return ChildrenCollection
     */
    public function getChildren();

    /**
     * Return true if the document has children, false if not.
     *
     * @return boolean
     */
    public function hasChildren();

    /**
     * Return the locale of this document
     */
    public function getLocale();

    /**
     * Set the locale of this document
     *
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * Return the structure type
     *
     * @return string
     */
    public function getStructureType();

    /**
     * Set the structure type (e.g. overview)
     *
     * @param string
     */
    public function setStructureType($structureType);

    /**
     * Return the ID of the user that created this document
     *
     * @return integer|null ID of user or NULL if created by the system
     */
    public function getCreator();

    /**
     * Set the creator ID
     *
     * @param integer
     */
    public function setCreator($creator);

    /**
     * Return the ID of the user that last changed this document
     *
     * @return integer|null ID of user or NULL if initially created by the system
     */
    public function getChanger();

    /**
     * Set the changer ID
     *
     * @param integer $changer
     */
    public function setChanger($changer);

    /**
     * Return the created date
     *
     * @return \DateTime
     */
    public function getCreated();

    /**
     * Set the created date
     *
     * @param \DateTime
     */
    public function setCreated(\DateTime $created);

    /**
     * Set the date the document was last changed
     *
     * @return \DateTime
     */
    public function getChanged();
    
    /**
     * Set the date the document was last changed
     *
     * @param mixed $changed
     */
    public function setChanged(\DateTime $changed);

    /**
     * Return the structure content for this document
     *
     * @return array
     */
    public function getContent();

    /**
     * Set the structure content for this document
     *
     * @param array $content
     */
    public function setContent($content);

    /**
     * Return the name of the webspace to which this document belongs
     *
     * @return string
     */
    public function getWebspaceKey();

    /**
     * Return the PHPCR node (only available when the node is managed)
     *
     * @return PHPCR\NodeInterface
     */
    public function getPhpcrNode();

    /**
     * Return the document type, e.g. "page" or "snippet". This value
     * should be constant.
     *
     * @return string
     */
    public function getDocumentType();

    /**
     * Return the depth of this document within the content repository
     *
     * @return integer
     */
    public function getDepth();

    /**
     * Set the document node helper. This should be set by an event
     * subscriber on the onLoad workflow event.
     *
     * This should only be set one time and would ideally be a cosntuctor
     * argument. The implementing class should throw an exception if
     * a helper is already set.
     *
     * @throws RuntimeException If the helper has already been set.
     *
     * @param DocumentNodeHelper $helper
     *
     */
    public function setDocumentNodeHelper(DocumentNodeHelper $helper);

    /**
     * Return the current workflow state
     *
     * Return value should represent workflow states, e.g.
     * testing, review, published, etc.
     *
     * Note the return value may also be a numeric value.
     *
     * @return string
     */
    public function getWorkflowState();

    /**
     * Return the localization state of the document, possible
     * values:
     *
     * Could be one of: localized, ghost or shadow
     *
     * @return string
     */
    public function getLocalizationState();

    /**
     * Check if the localization state is the given state
     *
     * @throws InvalidArgumentException When the state is not known
     *
     * @return boolean
     */
    public function isLocalizationState($state);

    /**
     * Return all of the localizations for this document, including
     * "shadow" localizations.
     *
     * @return string[]
     */
    public function getLocales();

    /**
     * Static method to return the validation localization states for
     * this document. One of DocumentInterface::LOCALIZATION_STATE_*
     *
     * @return string[]
     */
    public static function getValidLocalizationStates();
}
