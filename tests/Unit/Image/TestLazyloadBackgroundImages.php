<?php

namespace RocketLazyload\Tests\Unit\Image;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;
use RocketLazyload\Image;

/**
 * @covers RocketLazyload\Image::lazyloadBackgroundImages
 *
 * @group Image
 */
class TestLazyloadBackgroundImages extends TestCase {
    private $image;

    public function setUp() {
        parent::setUp();
        Monkey\setUp();
        $this->image = new Image();
    }

    public function tearDown() {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testShouldReturnSameWhenNoBackgroundImage() {
        $noimage = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/noimage.html');

        $this->assertSame(
            $noimage,
            $this->image->lazyloadBackgroundImages($noimage, $noimage)
        );
    }

    public function testShouldReturnBackgroundImagesLazyloaded() {
        Functions\when('esc_attr')->returnArg();
        Functions\expect('esc_url')->andReturnUsing( function ( $url ){
            return str_replace(['"', "'"], ["&quot;", "&#39;"], $url );
        } );

        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/bgimages.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/bgimageslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadBackgroundImages($original, $original)
        );
    }
}
