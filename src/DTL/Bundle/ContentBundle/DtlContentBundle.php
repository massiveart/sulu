<?php

namespace DTL\Bundle\ContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use DTL\Bundle\ContentBundle\DependencyInjection\Compiler\FormPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DtlContentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FormPass());
    }
}
