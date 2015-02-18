<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Property;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\FrontView\FrontView;
use DTL\Bundle\ContentBundle\Document\PageDocument;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract test class for all content types
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
abstract class AbstractPropertyTypeTestCase extends SuluTestCase
{
    abstract protected function getTypeAlias();

    /**
     * Provider for testFormView
     *
     * @return array
     */
    abstract public function provideFormView();

    /**
     * Test the form view
     *
     * @dataProvider provideFormView
     *
     * @param $options Options for the content view
     * @param $expectedAttributes Expected form view variables
     */
    public function testFormView($options, $expectedVars)
    {
        $options = $this->completeOptions($options);
        $view = $this->createForm($options)->createView();
        $view = $view['test_type'];

        foreach ($expectedVars as $key => $value) {
            $this->assertEquals($value, $view->vars[$key]);
        }
    }

    /**
     * Provide for the content view test
     */
    abstract public function provideFrontViewAttributes();

    /**
     * Test that the view is properly configured
     *
     * @dataProvider provideFrontViewAttributes
     *
     * @param $options Options for the content view
     * @param $expectedAttributes Expected content view attributes
     */
    public function testFrontViewAttributes($options, $expectedAttributes)
    {
        $options = $this->completeOptions($options);
        $frontView = $this->createFrontView($this->getTypeAlias(), array(), $options);

        foreach ($expectedAttributes as $key => $value) {
            $this->assertEquals($value, $frontView->getAttribute($key));
        }
    }

    /**
     * Test that the form renders
     *
     * @dataProvider provideFormView
     *
     * @param array $options Form options
     */
    public function testRenderFormView($options)
    {
        $options = $this->completeOptions($options);
        $form = $this->createForm($options);
        $view = $form->createView();

        $twig = $this->getContainer()->get('twig');
        $twig->enableDebug();

        $result = $twig->render(__DIR__ . '/views/test.html.twig', array(
            'view' => $view['test_type'],
        ));

        $this->assertContains('data-mapper-property', $result);
    }

    /**
     * Provide data for testFormSubmit
     */
    public function provideFormSubmit()
    {
        return array(
            array(
                $this->completeOptions(array()),
                'hello',
                'hello',
            ),
        );
    }

    /**
     * Test form submission in mapping
     *
     * @param array $options Options for the form
     * @param mixed $data The data to submit to the form
     * @param mixed $expectedData The data that the form should map to the data
     *
     * @dataProvider provideFormSubmit
     */
    public function testFormSubmit($options, $data, $expectedData)
    {
        $content = new \ArrayObject();
        $options = $this->completeOptions($options);
        $form = $this->createForm($options);
        $form->setData($content);

        $form->submit(array(
            'test_type' => $data,
        ));

        if (!$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                var_dump($error->getCause());
            }
        }

        $this->assertTrue($form->isValid());
    }

    /**
     * Provide data for testFrontViewValue
     */
    abstract function provideFrontViewValue();

    /**
     * Assert the value of a content view
     *
     * Override assertFrontViewValue for non-scalar
     * comparisons.
     *
     * @dataProvider provideFrontViewValue
     *
     * @param array $options
     * @param mixed $data
     * @param mixed $expectedValue
     */
    public function testFrontViewValue(array $options, $data, $expectedValue)
    {
        $options = $this->completeOptions($options);
        $frontView = $this->createFrontView($this->getTypeAlias(), $data, $this->completeOptions($options));
        $this->assertFrontViewValue($frontView, $expectedValue);
    }

    /**
     * Assertion for the content view value test.
     *
     * Override to assert non-scalar values
     *
     * @param FrontView $view
     * @param mixed $expectedValue
     */
    public function assertFrontViewValue($view, $expectedValue)
    {
        $this->assertEquals($expectedValue, $view->getValue());
    }

    /**
     * Merge default options from the abstract type
     *
     * @param array $options
     *
     * @return array
     */
    protected function completeOptions(array $options)
    {
        return array_merge(array(
            'label' => array(
                'de' => 'Adresse',
                'en' => 'Resource Locator',
            ),
            'tags' => array(
                array(
                    'name' => 'sulu.rlp',
                ),
            ),
        ), $options);
    }

    /**
     * Create form with a single field "test_type" using this
     * tests content type.
     *
     * @param array $options Options for the content form
     *
     * @return FormInterface
     */
    private function createForm($options)
    {
        $form = $this->getContainer()->get('dtl_content.form.factory')->createBuilder()
            ->add('test_type', $this->getType(), $options)
            ->getForm();

        return $form;
    }

    private function createFrontView($propertyType, $data, $options)
    {
        $builder = $this->getContainer()->get('dtl_content.front_view.builder');
        return $builder->buildType($propertyType, $data, $options);
    }

    protected function getType()
    {
        return $this->getContainer()->get('dtl_content.type.registry')->getType($this->getTypeAlias());
    }
}
