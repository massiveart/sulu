<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Component\Content\FrontView\FrontView;

class BlockType extends AbstractContentType
{
    private $frontBuilder;

    public function __construct(FrontViewBuilder $frontBuilder)
    {
        $this->frontBuilder = $frontBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function setFormDefaultOptions(OptionsResolverInterface $options)
    {
        parent::setFormDefaultOptions($options);

        $options->setDefaults(array(
            'min_occurs' => 1,
            'max_occurs' => 1,
        ));

        $options->setRequired(array(
            'default_type',
            'prototypes',
        ));

        $options->setTypes(array(
            'prototypes' => 'array',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['prototypes'] as $name => $prototype) {
            $prototypeBuilder = $builder->create($name, $prototype['type'], $prototype['options']);
            foreach ($prototype['properties'] as $propName => $prop) {
                $prototypeBuilder->add($propName, $prop['type'], $prop['options']);
            }
            $builder->add($prototypeBuilder->getForm());
        }
    }

    public function buildFrontView(FrontView $view, $data, $options)
    {
        // how to get the default_type from the form configuration??
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'block';
    }
}

