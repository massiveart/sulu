<?php

namespace DTL\Bundle\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register the (content) form types for the ContentBundle
 *
 * We use our own form stack to avoid conflicts with the native form
 * component.
 */
class FormPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('dtl_content.form.extension')) {
            return;
        }

        $definition = $container->getDefinition('dtl_content.form.extension');

        $types = array();

        foreach ($container->findTaggedServiceIds('dtl_content.form.type') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;

            // Flip, because we want tag aliases (= type identifiers) as keys
            $types[$alias] = $serviceId;
        }

        // add the base form type
        $types['form'] = 'form.type.form';

        $definition->replaceArgument(1, $types);

        foreach ($container->findTaggedServiceIds('form.type_extension') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;

            $typeExtensions[$alias][] = $serviceId;
        }

        $definition->replaceArgument(2, $typeExtensions);
    }
}
