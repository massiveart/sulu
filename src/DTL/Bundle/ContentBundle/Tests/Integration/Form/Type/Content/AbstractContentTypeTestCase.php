<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration\Form\Type\Content;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\Form\FormInterface;

abstract class AbstractContentTypeTestCase extends SuluTestCase
{
    protected function createForm($options)
    {
        $form = $this->getContainer()->get('form.factory')->createBuilder()
            ->add($this->getType(), $this->getType(), $options)
            ->getForm();

        return $form;
    }

    protected function renderForm(FormInterface $form)
    {
        $view = $form->createView();

        $templating = $this->getContainer()->get('twig');
        return $templating->render(__DIR__ . '/views/test.html.twig', array(
            'view' => $view[$this->getType()],
        ));
    }

    protected function assertView(FormInterface $form, $expectedView, $tokens)
    {
        $view = $this->renderForm($form);
        foreach ($tokens as $token => $value) {
            $expectedView = str_replace($token, $value, $expectedView);
        }

        $view = str_replace(' ', '', trim($view));
        $expectedView = str_replace(' ', '', trim($expectedView));

        $this->assertEquals($expectedView, $view);
    }
}
