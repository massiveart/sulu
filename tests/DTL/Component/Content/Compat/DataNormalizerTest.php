<?php

namespace vendor\sulu\sulu\tests\DTL\Component\Content\Compat;

use DTL\Component\Content\Compat\DataNormalizer;

class DataNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->normalizer = new DataNormalizer();
    }

    public function provideNormalizer()
    {
        return array(
            array(
                array(
                    'title' => 'Title',
                    'url' => '/path/to',
                    'nodeType' => 'external',
                    'navContexts' => array('one', 'two'),
                    'nodeState' => 2,
                    'animal' => 'dog',
                    'car' => 'skoda',
                    'duck' => 'quack',
                ),
                array(
                    'title' => 'Title',
                    'resourceLocator' => '/path/to',
                    'redirectType' => 'external',
                    'lifecycleStage' => 2,
                    'navigationContexts' => array('one', 'two'),
                    'content' => array(
                        'animal' => 'dog',
                        'car' => 'skoda',
                        'duck' => 'quack',
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider provideNormalizer
     */
    public function testNormalizer($data, $expectedNormalizedData)
    {
        $this->assertEquals(
            $expectedNormalizedData,
            $this->normalizer->normalize($data)
        );
    }
}
