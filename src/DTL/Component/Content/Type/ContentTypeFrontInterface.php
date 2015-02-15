<?php

namespace DTL\Component\Content\Type;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DTL\Component\Content\FrontView\FrontView;
use DTL\Component\Content\Document\DocumentInterface;

interface ContentTypeFrontInterface
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
    public function buildFrontView(FrontView $view, $data);

    /**
     * Configure the options which should be available when rendering the
     * front view.
     *
     * @param OptionsResolverInterface $options
     */
    public function setDefaultFrontOptions(OptionsResolverInterface $options);
}
