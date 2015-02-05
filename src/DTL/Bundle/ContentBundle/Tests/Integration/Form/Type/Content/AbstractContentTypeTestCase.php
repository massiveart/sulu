<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\Form\ContentView;
use DTL\Bundle\ContentBundle\Document\PageDocument;

/**
 * Abstract test class for all content types
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
abstract class AbstractContentTypeTestCase extends SuluTestCase
{
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
    abstract public function provideContentViewAttributes();

    /**
     * Test that the view is properly configured
     *
     * @dataProvider provideContentViewAttributes
     *
     * @param $options Options for the content view
     * @param $expectedAttributes Expected content view attributes
     */
    public function testContentViewAttributes($options, $expectedAttributes)
    {
        $options = $this->completeOptions($options);
        $form = $this->createForm($options);
        $contentView = new ContentView();
        $this->getType()->buildContentView($contentView, $form);

        foreach ($expectedAttributes as $key => $value) {
            $this->assertEquals($value, $contentView->getAttribute($key));
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

    abstract function provideContentViewValue();

    /**
     * Assert the value of a content view
     *
     * Override assertContentViewValue for non-scalar
     * comparisons.
     *
     * @dataProvider provideContentViewValue
     *
     * @param array $options
     * @param mixed $data
     * @param mixed $expectedValue
     */
    public function testContentViewValue(array $options, $data, $expectedValue)
    {
        $options = $this->completeOptions($options);
        $form = $this->createForm($options);

        $contentView = new ContentView();

        $formType = $form['test_type']->getConfig()->getType();
        $this->assertInstanceOf('DTL\Component\Content\Form\ContentResolvedTypeInterface', $formType);
        $formType->buildContentView($contentView, $data);
        $this->assertContentViewValue($contentView, $expectedValue);
    }

    /**
     * Assertion for the content view value test.
     *
     * Override to assert non-scalar values
     *
     * @param ContentView $view
     * @param mixed $expectedValue
     */
    public function assertContentViewValue($view, $expectedValue)
    {
        $this->assertEquals($expectedValue, $view->getValue());
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

    /**
     * Merge default options from the abstract type
     *
     * @param array $options
     *
     * @return array
     */
    private function completeOptions($options)
    {
        return array_merge(array(
            'locale' => 'de',
            'webspace_key' => 'sulu_io',
            'labels' => array(
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
}
