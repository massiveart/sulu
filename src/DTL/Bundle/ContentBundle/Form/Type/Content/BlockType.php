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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DTL\Component\Content\FrontView\FrontViewBuilder;

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
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        parent::setDefaultOptions($options);
        $options->setRequired(array(
            'default_type',
            'prototypes',
        ));

        $options->setAllowedTypes(array(
            'prototypes' => 'array',
        ));

        $options->setNormalizer('prototypes', function ($options, $prototypes) {
            $normalizedPrototypes = array();

            foreach ($prototypes as $prototype) {
                $normalizedPrototypes[] = array_merge(array(
                    'type' => null,
                    'options' => array(),
                    'properties' => array(),
                ), $prototype);
            }

            return $normalizedPrototypes;
        });

    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['prototypes'] as $name => $prototype) {
            $prototypeBuilder = $builder->create($name, 'form', $prototype['options']);
            foreach ($prototype['properties'] as $propName => $prop) {

                $propOptions = $prop['options'];
                $propOptions['webspace_key'] = $options['webspace_key'];
                $propOptions['locale'] = $options['locale'];

                $prototypeBuilder->add($propName, $prop['type'], $propOptions);
            }
            $builder->add($prototypeBuilder);
        }

        // handle resize .. how does current system do it?
    }

    public function buildFrontView(FrontView $view, $data, array $options)
    {
        $defaultType = $options['default_type'];
        $prototypes = $options['prototypes'];
        $children = array();

        foreach ($data as $prototypeName => $content) {

            // if the prototype is not defined then it means the user
            // has removed its definition from the structure resource but the
            // content repository still has a reference to it. we must allow this.
            if (!isset($prototypes[$prototypeName])) {
                continue;
            }

            $prototype = $prototypes[$prototypeName];
            $child = $this->frontBuilder->buildFromProperties($prototype['properties'], $content);
            $children[] = $view;
        }

        $view->setChildren($children);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'block';
    }
}
