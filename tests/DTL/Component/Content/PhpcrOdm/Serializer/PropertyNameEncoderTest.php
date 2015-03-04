<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\PhpcrOdm\Serializer;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Component\Content\PhpcrOdm\Serializer\PropertyNameEncoder;

class PropertyNameEncoderTest extends ProphecyTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->encoder = new PropertyNameEncoder(
            'i18n',
            'cont'
        );
    }

    public function testEncodeLocalized()
    {
        $res = $this->encoder->encodeLocalized('prop', 'de');
        $this->assertEquals('i18n:de-prop', $res);
    }

    public function testEncode()
    {
        $res = $this->encoder->encode('prop');
        $this->assertEquals('cont:prop', $res);
    }
}

