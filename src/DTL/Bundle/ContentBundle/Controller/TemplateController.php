<?php

namespace DTL\Bundle\ContentBundle\Controller;

use DTL\Component\Content\Structure\Form\StructureFormTypeFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Response;

class TemplateController
{
    private $twig;
    private $formFactory;
    private $tokenStorage;

    public function __construct(
        \Twig_Environment $twig,
        StructureFormTypeFactory $formFactory,
        TokenStorage $tokenStorage

    )
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function templateAction(Request $request)
    {
        $webspace = $request->get('webspace');
        $locale = $request->get('language');
        $documentType = $request->get('type', 'page');
        $structureType = $request->get('key');

        if ($structureType === null) {
            $structureType = $this->container->getParameter('sulu.content.structure.default_type.page');
        }

        $user = $this->tokenStorage->getToken()->getUser();
        $userLocale = $user->getLocale();

        $form = $this->formFactory->create($documentType, $structureType, array(
            'locale' => 'de',
            'webspace_key' => 'sulu_io',
        ));

        $this->twig->addGlobal('webspace', $webspace);
        $this->twig->addGlobal('locale', $locale);

        $response = new Response($this->twig->render(
            'DtlContentBundle::structure.html.twig',
            array(
                'form' => $form->createView(),
            )
        ));

        return $response;
    }
}
