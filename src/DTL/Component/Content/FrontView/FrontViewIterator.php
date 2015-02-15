<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\FrontView;

use Symfony\Component\Form\Exception\BadMethodCallException;

/**
 * The content view iterator is used to iterate over a collection of views.
 *
 * An example of this is given by the smart content content type which aggregates
 * a number of structure documents and allows the user to iterate over them.
 *
 * By creating a content view iterator the developer will ensure that views are
 * lazy-loaded.
 */
class FrontViewIterator implements \Iterator, \Countable
{
    /**
     * @var FrontViewBuilder
     */
    private $viewBuilder;

    /**
     * @var FrontDocument[]
     */
    private $documents;

    /**
     * @param FrontViewBuilder $viewBuilder
     * @param FrontDocument[] $documents
     */
    public function __construct(FrontViewBuilder $viewBuilder, $documents)
    {
        $this->viewBuilder = $viewBuilder;
        $this->documents = new \ArrayIterator($documents);
    }

    public function rewind()
    {
        $this->documents->rewind();
    }

    public function current()
    {
        return $this->viewBuilder->buildFor($this->documents->current());
    }

    public function next()
    {
        $this->documents->next();
    }

    public function key()
    {
        return $this->documents->key();
    }

    public function valid()
    {
        return $this->documents->valid();
    }

    /**
     * Implements \Countable.
     *
     * @return int The number of children views
     */
    public function count()
    {
        return $this->documents->count();
    }
}
