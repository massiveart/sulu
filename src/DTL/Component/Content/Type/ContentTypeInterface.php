<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Type;

use Symfony\Component\Form\FormTypeInterface;
use DTL\Component\Content\FrontView\FrontView;

/**
 * Form types implementing this interface become valid Sulu
 * content-types.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface ContentTypeInterface extends FormTypeInterface
{
    /**
     * Build the content front view.
     *
     * This is the data which will be finally available in the frontend
     * view of this content type.
     *
     * @param FrontView $view
     * @param mixed $data
     */
    public function buildFrontView(FrontView $view, $data, array $options);
}
