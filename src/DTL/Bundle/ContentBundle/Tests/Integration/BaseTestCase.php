<?php

namespace DTL\Bundle\ContentBundle\Tests\Integration;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use PHPCR\Util\NodeHelper;

class BaseTestCase extends SuluTestCase
{
    private $manager;

    protected function initPhpcr()
    {
        $this->manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        NodeHelper::purgeWorkspace($this->manager->getPhpcrSession());
        $this->manager->getPhpcrSession()->save();
        $this->getContainer()->get('doctrine_phpcr.initializer_manager')->initialize();
        $this->manager->flush();
    }

    protected function getDm()
    {
        if (!$this->manager) {
            throw new \InvalidArgumentException(
                'DocumentManager has not been initialized, execute ->initPhpcr()'
            );
        }

        return $this->manager;
    }
}
