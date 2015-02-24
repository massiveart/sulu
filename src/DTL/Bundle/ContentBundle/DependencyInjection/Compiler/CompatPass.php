<?php

namespace DTL\Bundle\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Replaces legacy services with compatibility layers
 */
class CompatPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->getParameter('compat')) {
            return;
        }

        $this->replaceStructureManager($container);
    }

    public function replaceStructureManager()
    {
        if (!$container->hasDefinition('sulu.content.structure_manager')) {
            return;
        }

        $container->removeDefinition('sulu.content.structure_manager');
        $container->setAlias('sulu.content.structure_manager', 'dtl_content.compat.structure.structure_manager');
    }
}

