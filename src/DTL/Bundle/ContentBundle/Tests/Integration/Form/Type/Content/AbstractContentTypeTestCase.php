<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;
use DTL\Component\Content\Form\ContentView;

abstract class AbstractContentTypeTestCase extends SuluTestCase
{
    protected function createForm($options)
    {
        $form = $this->getContainer()->get('form.factory')->createBuilder()
            ->add('test_type', $this->getType(), $options)
            ->getForm();

        return $form;
    }

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
    abstract public function provideContentView();

    /**
     * Test that the view is properly configured
     *
     * @dataProvider provideContentView
     *
     * @param $options Options for the content view
     * @param $expectedAttributes Expected content view attributes
     */
    public function testContentView($options, $expectedAttributes)
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

        $templating = $this->getContainer()->get('twig');
        return $templating->render(__DIR__ . '/views/test.html.twig', array(
            'view' => $view['test_type'],
        ));
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
